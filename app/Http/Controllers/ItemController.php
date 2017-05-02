<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use ItemService;
use HelperService;
use Constant;
use App\Item;
use App\JasaConfiguration;
use App\JasaIncentive;
use App\Branch;
use App\BranchStock;
use App\TransferStock;
use App\Employee;
use App\Stock;
use Sentinel;
use Carbon\Carbon;
use UserService;
use EmployeeService;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('superadmin')->only(['addItem','getItemJasaById', 'getItemProdukById', 'getItemSewaById', 'getJasaConfiguration']);

        // $this->middleware('log')->only('index');
        //
        // $this->middleware('subscribed')->except('store');
    }

    function addItem()
    {
        // return HelperService::itemTypeById(1);
        $item_types = [
            Crypt::encryptString(Constant::type_id_produk) => 'Produk',
            Crypt::encryptString(Constant::type_id_jasa) => 'Jasa',
            Crypt::encryptString(Constant::type_id_sewa) => 'Sewa',
        ];
        return view('item.add-item',[
            'item_types' => $item_types
        ]);
        // dd($item_types);
    }

    function updateItem(Request $request)
    {
        $inputs = $request->all();
        $item_id = Crypt::decryptString($inputs['item']);
        unset($inputs['item']);unset($inputs['_token']);

        $inputs['branch_price'] = str_replace('.', '', $inputs['branch_price']);
        $inputs['m_price'] = str_replace('.', '', $inputs['m_price']);
        $inputs['nm_price'] = str_replace('.', '', $inputs['nm_price']);
        $incentive = -1;
        if(isset($inputs['incentive'])) {
                $incentive = str_replace('.', '', $inputs['incentive']);
                unset($inputs['incentive']);
        }
        $inputs['created_by'] = Sentinel::getUser()->id;
        $item = Item::where('item_id', $item_id)->update($inputs);
        if($incentive != -1) {
            if(intval(ItemService::getLatestIncentive($item_id, '!obj')) != $incentive) {
                $create = [];
                $create['item_id_jasa'] = $item_id;
                $create['incentive'] = $incentive;
                $create['created_by'] = $inputs['created_by'];
                $create['valid_since'] = Carbon::now()->toDateString();

                $item_incentive = JasaIncentive::create($create);
            }
        }
        // $item->;
        return response()->json([
            'status' => 'success',
            'message' => 'Item berhasil diperbaharui!',
            'no_reset_form' => true
        ]);
    }

    function getItemProdukById($id)
    {
        $item = Item::where('item_id', $id)->first();

        if($item == null || $item->item_type != Constant::type_id_produk) {
            abort(404);
        }
        return view('item.edit-item',[
            'item' => $item
        ]);
    }

    function getItemJasaById($id)
    {
        $item = Item::where('item_id', $id)->first();

        if($item == null || $item->item_type != Constant::type_id_jasa) {
            abort(404);
        }
        return view('item.edit-item',[
            'item' => $item
        ]);
    }

    function getItemSewaById($id)
    {
        $item = Item::where('item_id', $id)->first();

        if($item == null || $item->item_type != Constant::type_id_sewa) {
            abort(404);
        }
        return view('item.edit-item',[
            'item' => $item
        ]);
    }
    // function editItem($item_id_encrypted, $url_ref)
    // {
    //     // return HelperService::itemTypeById(1);
    //     $item_types = [
    //         Crypt::encryptString(Constant::type_id_produk) => 'Produk',
    //         Crypt::encryptString(Constant::type_id_jasa) => 'Jasa',
    //         Crypt::encryptString(Constant::type_id_sewa) => 'Sewa',
    //     ];
    //     return view('item.edit-item',[
    //         'item_types' => $item_types
    //     ]);
    //     // dd($item_types);
    // }

    function addItemDo(Request $request)
    {
        $inputs = $request->all();
        $inputs['item_type'] = Crypt::decryptString($inputs['item_type']);
        unset($inputs['_token']);
        if(!empty(trim($inputs['branch_price']))) {
            $inputs['branch_price'] = str_replace('.', '', $inputs['branch_price']);
        }
        $inputs['m_price'] = str_replace('.', '', $inputs['m_price']);
        $inputs['nm_price'] = str_replace('.', '', $inputs['nm_price']);
        $add_item = ItemService::addItem($inputs);

        return response()->json([
            'status' => 'success',
            'message' => 'Item berhasil ditambahkan!'
        ]);
    }

    function getItemsProduk($page=1)
    {
        $take = 20;
        $skip = ($page - 1) * $take;
        $total = Item::where('item_type', Constant::type_id_produk)->count();

        $role_user = UserService::getRoleByUser();
        $items_produk= Item::where('item_type', Constant::type_id_produk)
                        ->skip($skip)->take($take)->orderBy('item_name');

        if(strtolower($role_user->slug) == 'superadmin') {
            $items_produk = $items_produk->get();
        }
        else if(strtolower($role_user->slug) == 'manager') {
            $employee_data = EmployeeService::getEmployeeByUser();
            $items_produk = $items_produk->with(['branchStock' => function ($query) use ($employee_data) {
                                $query->where('branch_id', $employee_data->branch_id);
                            }])->get();
        }
        else {
            abort(404);
        }

        if($items_produk->count()) {
            return view('item.items-produk', [
                'items_produk' => $items_produk,
                'message' => HelperService::dataCountingMessage($total, $skip+1, $skip+$items_produk->count(), $page),
                'total_page' => ceil($total/$take),
                'role_user' => $role_user
            ]);
        }
        abort(404);
    }

    function getItemsJasa($page=1)
    {
        $take = 20;
        $skip = ($page - 1) * $take;
        $total = Item::where('item_type', Constant::type_id_jasa)->count();
        $items_jasa = Item::where('item_type', Constant::type_id_jasa)
                        ->skip($skip)->take($take)
                        ->orderBy('item_name')->get();
        if($items_jasa->count())
            return view('item.items-jasa', [
                'items_jasa' => $items_jasa,
                'message' => HelperService::dataCountingMessage(Item::where('item_type', Constant::type_id_jasa)->count(), $skip+1, $skip+$items_jasa->count(), $page),
                'total_page' => ceil($total/$take),
                'role_user' => UserService::getRoleByUser()
            ]);

        abort(404);
    }

    function getItemsSewa($page=1)
    {
        $take = 20;
        $skip = ($page - 1) * $take;
        $total = Item::where('item_type', Constant::type_id_sewa)->count();
        $role_user = UserService::getRoleByUser();
        $items_sewa = Item::where('item_type', Constant::type_id_sewa)
                        ->skip($skip)->take($take)
                        ->orderBy('item_name');

        if(strtolower($role_user->slug) == 'superadmin') {
            $items_sewa = $items_sewa->get();
        }
        else if(strtolower($role_user->slug) == 'manager'){
            $employee_data = EmployeeService::getEmployeeByUser();
            $items_sewa = $items_sewa->with(['branchStock' => function ($query) use ($employee_data) {
                                $query->where('branch_id', $employee_data->branch_id);
                            }])->get();
        }
        else {
            abort(404);
        }
        if($items_sewa->count())
            return view('item.items-sewa', [
                'items_sewa' => $items_sewa,
                'message' => HelperService::dataCountingMessage(Item::where('item_type', Constant::type_id_sewa)->count(), $skip+1, $skip+$items_sewa->count(), $page),
                'total_page' => ceil($total/$take),
                'role_user' => $role_user
            ]);

        abort(404);
    }

    function getJasaConfiguration($item_id)
    {
        $item_jasa = Item::where('item_id', $item_id)->first()
                            ->where('item_type', Constant::type_id_jasa)->first();
        if($item_jasa) {
            $jasa_configurations = JasaConfiguration::where('item_id_jasa', $item_id)
                            ->with(['produk'])->orderBy('created_at', 'desc')->get();
            $produk_exists = $jasa_configurations->pluck('item_id_produk')->toArray();
            // dd($jasa_configurations);
            return view('item.configure-jasa', [
                'item_jasa' => $item_jasa,
                'jasa_configurations' => $jasa_configurations,
                'items_produk' => Item::where('item_type', Constant::type_id_produk)
                                        ->whereNotIn('item_id', $produk_exists)->get()
            ]);
        }
        abort(404);
    }

    function deleteJasaConfiguration($id_encrypted)
    {
        $config_id = Crypt::decryptString($id_encrypted);
        $config = JasaConfiguration::find($config_id);

        $back_to = route('configure.item.jasa',[
            'item_id' => $config->item_id_jasa
        ]);

        $config->delete();

        return redirect($back_to);
    }

    function updateJasaConfiguration(Request $request, $id_encrypted)
    {
        $inputs = $request->all();

        $id_decrypted = Crypt::decryptString($id_encrypted);

        $inputs['configured_by'] = Sentinel::getUser()->id;
        unset($inputs['_token']);
        if(strpos($id_decrypted, 'add_new_configuration') !== false) {
            $split = explode('.', $id_decrypted);
            // dd($split);
            $inputs['item_id_jasa'] = $split[1];
            $inputs['item_id_produk'] = Crypt::decryptString($inputs['item_produk']);
            unset($inputs['item_produk']);
            // dd($inputs);
            $add_config = JasaConfiguration::create($inputs);
            return response()->json([
                'status' => 'success',
                'need_reload' => true,
                'message' => 'Konfigurasi berhasil ditambahkan!'
            ]);
        }

        $config = JasaConfiguration::find($id_decrypted)->update($inputs);
        return response()->json([
            'status' => 'success',
            'no_reset_form' => true,
            'message' => 'Konfigurasi berhasil diupdate!'
        ]);

    }

    function inputStock()
    {
        $item = Item::where('item_id', request()->item_id)->with(['itemStock'])->first();

        if($item==null || $item->item_type==Constant::type_id_jasa){
            abort(404);
        }

        return view('item.input-stock',[
            'item' => $item,
            'stock' => $item->itemStock == null ? 0 : $item->itemStock->stock
        ]);
    }


    function inputStockDo(Request $request)
    {
        $stock = Stock::where('item_id', request()->item_id)->first();
        $inputs = $request->all();
        if($stock==null) {
            $stock = new Stock();
            $stock->item_id = request()->item_id;
            $stock->stock = 0;
        }
        $stock->stock = $stock->stock + intval($inputs['add_stock']);
        $stock->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Stok item di pusat berhasil diupdate',
            'need_reload' => true
        ]);
    }

    function supplyBranch()
    {
        $item = Item::where('item_id', request()->item_id)->with(['itemStock'])->first();

        if($item==null || $item->item_type==Constant::type_id_jasa) {
            abort(404);
        }
        if(request()->branch_id == 'choose_branch') {
            return view('item.pra-supply-branch',[
                'stock_pusat' => $item->itemStock == null ? 0 : $item->itemStock->stock,
                'item' => $item,
                'branches' => Branch::all()
            ]);
        }
        else {
            $branch_id = intval(Crypt::decryptString(request()->branch_id));
            $branch = Branch::find($branch_id);

            if($branch==null){
                abort(404);
            }

            $branch_stock = BranchStock::where('item_id', request()->item_id)
                                    ->where('branch_id', $branch_id)->first();

            return view('item.supply-branch',[
                'item' => $item,
                'stock_pusat' => $item->itemStock == null ? 0 : $item->itemStock->stock,
                'branch' => $branch,
                'branch_stock' => $branch_stock == null ? 0 : $branch_stock->stock,
            ]);
        }
    }

    function supplyBranchDo(Request $request)
    {
        $inputs = $request->all();
        $transfer_inputs['item_id'] = request()->item_id;
        $transfer_inputs['branch_id'] = intval(Crypt::decryptString(request()->branch_id));
        $transfer_inputs['stock'] = intval($inputs['add_stock']);

        $item = Item::where('item_id', request()->item_id)->with(['itemStock'])->first();
        $stock_pusat = $item->itemStock == null ? 0 : $item->itemStock->stock;

        if($stock_pusat<$transfer_inputs['stock']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Stok yang disupply ke cabang tidak dapat melebihi jumlah stok yang ada di pusat!',
            ]);
        }

        $transfer_inputs['sender'] = Sentinel::getUser()->id;
        $transfer_inputs['sender_note'] = $inputs['note'];
        $transfer_stock = TransferStock::create($transfer_inputs);
        $item->itemStock->stock = $item->itemStock->stock-$transfer_inputs['stock'];
        $item->itemStock->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Stok item di cabang akan bertambah setelah dikonfirmasi oleh Manager.<br/>Stok dipusat akan berkurang selama konfirmasi Pending dan dapat bertambah kembali jika Rejected, namun akan tetap berkurang jika sudah Accepted.',
            'need_reload' => true
        ]);
    }

    function getApprovedConfirmations()
    {
        $approved_confirmations = ItemService::getApprovedStockConfirmation();

        return view('item.approved-stock-confirmation',[
            'approved_confirmations' => $approved_confirmations
        ]);
    }

    function getRejectedConfirmations()
    {
        $rejected_confirmations = ItemService::getRejectedStockConfirmation();

        return view('item.rejected-stock-confirmation',[
            'rejected_confirmations' => $rejected_confirmations
        ]);
    }


    function getPendingConfirmations()
    {
        $pending_confirmations = ItemService::getPendingStockConfirmation();

        return view('item.pending-stock-confirmation',[
            'pending_confirmations' => $pending_confirmations
        ]);
    }

    function doConfirmPendingConfirmation(Request $request)
    {
        $inputs = $request->all();
        // dd($inputs);
        $pending_confirmation = TransferStock::find($inputs['confirmation_id']);
        if($pending_confirmation) {
            if($pending_confirmation->approval_status == Constant::status_pending) {
                //kalau pending bisa dikonfirmasi
                $inputs['confirmation_type'] = intval($inputs['confirmation_type']);
                if($inputs['confirmation_type'] == Constant::status_approved || $inputs['confirmation_type'] == Constant::status_rejected) {
                    $pending_confirmation->approval_status = $inputs['confirmation_type'];
                    $pending_confirmation->approval = Sentinel::getUser()->id;
                    $pending_confirmation->approval_note = trim($inputs['note']);
                    if($pending_confirmation->save()) {
                        if($pending_confirmation->approval_status == Constant::status_rejected) {
                            //balikin stok pusat
                            $stock_pusat = Stock::where('item_id', $pending_confirmation->item_id)->first();
                            $stock_pusat->stock = $stock_pusat->stock + $pending_confirmation->stock;
                            $stock_pusat->save();
                        }
                        else {
                            //kalo approved tambahin branch stock
                            $branch_stock = BranchStock::where('item_id', request()->item_id)
                                                    ->where('branch_id', $pending_confirmation->branch_id)->first();
                            if($branch_stock==null) {
                                $branch_stock = new BranchStock();
                                $branch_stock->item_id = $pending_confirmation->item_id;
                                $branch_stock->branch_id = $pending_confirmation->branch_id;
                                $branch_stock->stock = 0;
                            }
                            $branch_stock->stock = $branch_stock->stock + $pending_confirmation->stock;
                            $branch_stock->save();
                        }

                        return response()->json([
                            'status' => 'success',
                            'message' => 'Konfirmasi berhasil!',
                            'need_reload' => true
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Konfirmasi gagal!',
            'need_reload' => true
        ]);

        dd($pending_confirmation);
    }
}
