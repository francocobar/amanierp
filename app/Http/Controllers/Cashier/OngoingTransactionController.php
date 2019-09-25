<?php

namespace App\Http\Controllers\Cashier;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Sentinel;
use App\Model\Cashier\HeaderOngoing;
use App\TransactionDetail;
use App\Branch;
use App\Member;
use UserService;
use EmployeeService;
use Illuminate\Support\Facades\Cache;
use App\Helper\ItemHelper;
use App\Helper\CouponHelper;
use Carbon\Carbon;

class OngoingTransactionController extends Controller
{
    protected $error_message = '';
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('checkrole_cashier_sa_manager');
    }


    //transaksi berjalan berdasarkan id
    protected function getById(HeaderOngoing $header)
    {
        if($header->already_finish == 0 && $header->cashier_user_id == Sentinel::getUser()->id) {

            // Cache::put('list_item_ongoing'.$header->id, [], 1440);
            //
            //             return array('data'=>$return);
            $return['is_series'] = $header->is_series;
            if($header->is_series) {
                return array('data'=>$return);
            }
            $header_data['trans_id'] = $header->id;
            $header_data['member_id'] = $header->member_id ? $header->member_id : 'Guest';
            $header_data['customer_name'] = $header->customer_name;
            $return['header_data'] = $header_data;
            $return['detail_data'] = Cache::get('list_item_ongoing'.$header->id);
            $return['coupon_data'] = null;
            if($header->coupon_id) {
                $return['coupon_data'] = CouponHelper::getCouponById($header->coupon_id);
            }

            return array('data'=>$return);
        }
        return response(array('error'=>'401 Unauthorized'), 401);
    }

    //hapus transaksi berjalan berdasarkan id
    protected function removeById(HeaderOngoing $header)
    {
        if($header->already_finish == 0 && $header->cashier_user_id == Sentinel::getUser()->id) {

            // Cache::put('list_item_ongoing'.$header->id, [], 1440);
            //
            //             return array('data'=>$return);
            $header->delete();

            return array('data'=>true);
        }
        return response(array('error'=>'401 Unauthorized'), 401);
    }

    //add //transaksi berjalan berdasarkan id
    protected function addItem(HeaderOngoing $header)
    {
        if($header->already_finish == 0 && ($header->cashier_user_id == Sentinel::getUser()->id || UserService::isSuperadmin())) {
            $flag = decrypt(request()->flencry);
            if($header->branch_id == $flag['branch']) {
                //get item detail by item code
                $item_info = ItemHelper::getItemInfoByItemCode(trim(request()->item_code));
                $item_price = ItemHelper::getItemPriceByItemIdAndBranch($item_info->id, $header->branch_id);

                $new_list['item_name'] = $item_info->item_name;
                $new_list['item_code'] = $item_info->item_id;
                $new_list['item_qty'] = intval(request()->item_qty);
                if($header->member_id) {
                    $new_list['item_price'] = floatval($item_price->m_price);
                }
                else {
                    $new_list['item_price'] = floatval($item_price->nm_price);
                }

                $list = Cache::get('list_item_ongoing'.$header->id, []);
                $list[] = $new_list;

                Cache::put('list_item_ongoing'.$header->id, $list, 1440);
                return array('data'=> $new_list);
            }

        }
        return response(array('error'=>'401 Unauthorized'), 401);
    }

    protected function updateItem(HeaderOngoing $header)
    {
        if($header->already_finish == 0 && ($header->cashier_user_id == Sentinel::getUser()->id || UserService::isSuperadmin())) {
            $list = Cache::get('list_item_ongoing'.$header->id, []);
            foreach ($list as $key => $item) {
                if($item['item_code'] == request()->item_number) {
                    $list[$key]['item_qty'] = request()->item_qty;
                    Cache::put('list_item_ongoing'.$header->id, $list, 1440);
                    return array('data'=>true);
                }
            }
            return array('data'=>true);
        }
        return response(array('error'=>'401 Unauthorized'), 401);
    }

    protected function removeItem($item_code, HeaderOngoing $header)
    {
        if($header->already_finish == 0 && ($header->cashier_user_id == Sentinel::getUser()->id || UserService::isSuperadmin())) {
            $list = Cache::get('list_item_ongoing'.$header->id, []);

            foreach ($list as $key => $value) {
                if($value['item_code'] == $item_code) {
                    unset($list[$key]);
                    Cache::put('list_item_ongoing'.$header->id, $list, 1440);
                    return array('data'=> true);
                }
            }
            return array('data'=> false, 'message'=> 'Gagal menghapus item, Silahkan coba klik lagi!');
        }
        return response(array('error'=>'401 Unauthorized'), 401);
    }

    protected function applyCoupon(HeaderOngoing $header)
    {
        if(trim(request()->code)) {
            $coupon_flag = CouponHelper::getCouponByCodeAndValitAtDatetime(request()->code, Carbon::now()->toDateTimeString());
            if($coupon_flag) {
                $header->coupon_id = $coupon_flag->id;
                $header->save();
                return array('data' => $coupon_flag);
            }
            else {
                return array('data' => $coupon_flag, 'message' => 'Kode Kupon tidak valid');
            }
        }
        $header->coupon_id = null;
        $header->save();
        return array('data' => null);

    }
}
