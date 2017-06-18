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
use App\BranchModalLog;
use Sentinel;
use Carbon\Carbon;
use UserService;
use EmployeeService;
use App\PaketConfiguration;
use StockService;
use Illuminate\Support\Facades\DB;
use App\BranchStockLog;
use App\PembukuanPusat;
use App\PembukuanBranch;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('superadmin')->only(['addItem','getItemJasaById', 'getItemProdukById', 'getItemSewaById',
        'getJasaConfiguration', 'getItemPaketById', 'getPaketConfiguration']);

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
            Crypt::encryptString(Constant::type_id_paket) => 'Paket'
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

        if(isset($inputs['branch_price']) && !empty(trim($inputs['branch_price'])))
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

    function getItemPaketById($id)
    {
        $item = Item::where('item_id', $id)
                ->where('item_type', Constant::type_id_paket)->first();

        if($item == null) {
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
        if(intval($inputs['item_type']) == 1 || intval($inputs['item_type']) == 3) {
            if(!empty(trim($inputs['branch_price'])))
                $inputs['branch_price'] = str_replace('.', '', $inputs['branch_price']);
            else {
                return "error";
            }
        }
        else {

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
                        ->orderBy('item_name');
        $not_configured_items = [];
        if(request()->notconfiguredyet=='1') {
            $not_configured_items = JasaConfiguration::get(['item_id_jasa'])->pluck('item_id_jasa')->toArray();
            $items_jasa = $items_jasa->whereNotIn('item_id', $not_configured_items);
        }
        $counter = Item::where('item_type', Constant::type_id_jasa)->whereNotIn('item_id', $not_configured_items)->count();
         $items_jasa =  $items_jasa->get();
        if($items_jasa->count())
            return view('item.items-jasa', [
                'items_jasa' => $items_jasa->get(),
                'message' => HelperService::dataCountingMessage($counter, $skip+1, $skip+$items_jasa->count(), $page),
                'total_page' => ceil($total/$take),
                'role_user' => UserService::getRoleByUser()
            ]);

        abort(404);
    }

    function getItemsPaket($page=1)
    {
        $take = 20;
        $skip = ($page - 1) * $take;
        $total = Item::where('item_type', Constant::type_id_paket)->count();
        $items_paket = Item::where('item_type', Constant::type_id_paket)
                        ->skip($skip)->take($take)
                        ->orderBy('item_name');

        $not_configured_items = [];
        if(request()->notconfiguredyet=='1') {
            $not_configured_items = PaketConfiguration::get(['item_id_paket'])->pluck('item_id_paket')->toArray();
            $items_paket = $items_paket->whereNotIn('item_id', $not_configured_items);
        }
        $counter = Item::where('item_type', Constant::type_id_paket)->whereNotIn('item_id', $not_configured_items)->count();
        if($items_paket->count())
            return view('item.items-paket', [
                'items_paket' => $items_paket->get(),
                'message' => HelperService::dataCountingMessage($counter, $skip+1, $skip+$items_paket->count(), $page),
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
        $item_jasa = Item::where('item_id', $item_id)->where('item_type', Constant::type_id_jasa)->first();
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

    function deletePaketConfiguration($id_encrypted)
    {
        $config_id = Crypt::decryptString($id_encrypted);
        $config = PaketConfiguration::find($config_id);

        $back_to = route('configure.item.paket',[
            'item_id' => $config->item_id_paket
        ]);

        $config->delete();

        return redirect($back_to);
    }

    function updatePaketConfiguration(Request $request, $id_encrypted)
    {
        $inputs = $request->all();

        $id_decrypted = Crypt::decryptString($id_encrypted);

        $inputs['configured_by'] = Sentinel::getUser()->id;
        unset($inputs['_token']);
        if(strpos($id_decrypted, 'add_new_configuration') !== false) {
            $split = explode('.', $id_decrypted);
            // dd($split);
            $inputs['item_id_paket'] = $split[1];
            $inputs['item_id_jasa'] = Crypt::decryptString($inputs['item_jasa']);
            unset($inputs['item_jasa']);
            // dd($inputs);
            $add_config = PaketConfiguration::create($inputs);
            return response()->json([
                'status' => 'success',
                'need_reload' => true,
                'message' => 'Konfigurasi berhasil ditambahkan!'
            ]);
        }

        $config = PaketConfiguration::find($id_decrypted)->update($inputs);
        return response()->json([
            'status' => 'success',
            'no_reset_form' => true,
            'message' => 'Konfigurasi berhasil diupdate!'
        ]);
    }

    function getPaketConfiguration($item_id)
    {
        $item_paket = Item::where('item_id', $item_id)->where('item_type', Constant::type_id_paket)->first();
        if($item_paket) {
            $paket_configurations = PaketConfiguration::where('item_id_paket', $item_id)
                            ->with(['jasa'])->orderBy('created_at', 'desc')->get();
            $jasa_exists = $paket_configurations->pluck('item_id_jasa')->toArray();
            // dd($paket_configurations);
            return view('item.configure-paket', [
                'item_paket' => $item_paket,
                'paket_configurations' => $paket_configurations,
                'items_paket' => Item::where('item_type', Constant::type_id_jasa)
                                        ->whereNotIn('item_id', $jasa_exists)->get()
            ]);
        }
        abort(404);
    }

    function inputStock()
    {
        $item = Item::where('item_id', request()->item_id)->with(['itemStock'])->first();

        if($item==null || $item->item_type==Constant::type_id_jasa){
            abort(404);
        }
        if($item->itemStock) {
            return view('item.input-stock',[
                'item' => $item,
                'stock' => $item->itemStock->stock,
                'modal_per_pcs' => $item->itemStock->modal_per_pcs
            ]);
        }
        return view('item.input-stock',[
            'item' => $item,
            'stock' => 0,
            'modal_per_pcs' => 0
        ]);
    }


    function inputStockDo(Request $request)
    {
        $inputs = $request->all();
        $inputs['item_id'] = request()->item_id;
        DB::beginTransaction();
        if(StockService::inputStockPusat($inputs) == '') {
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Stok item di pusat berhasil diupdate',
                'need_reload' => true
            ]);
        }
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
        $transfer_inputs['modal_pusat'] = $item->itemStock->modal_per_pcs;
        $transfer_inputs['modal_cabang'] = $item->branch_price;
        // dd($transfer_inputs);

        DB::beginTransaction();
        $transfer_stock = TransferStock::create($transfer_inputs);
        $item->itemStock->stock = $item->itemStock->stock-$transfer_inputs['stock'];
        $item->itemStock->save();
        DB::commit();
        return response()->json([
            'status' => 'success',
            'message' => 'Stok item di cabang akan bertambah setelah dikonfirmasi oleh Manager.<br/>Stok dipusat akan berkurang selama konfirmasi Pending dan dapat bertambah kembali jika Rejected, namun akan tetap berkurang jika sudah Accepted.',
            'need_reload' => true
        ]);
    }

    function getApprovedConfirmations()
    {
        $approved_confirmations = StockService::getApprovedStockConfirmation();

        return view('item.approved-stock-confirmation',[
            'approved_confirmations' => $approved_confirmations
        ]);
    }

    function getRejectedConfirmations()
    {
        $rejected_confirmations = StockService::getRejectedStockConfirmation(request()->unseen);

        return view('item.rejected-stock-confirmation',[
            'rejected_confirmations' => $rejected_confirmations
        ]);
    }


    function getPendingConfirmations()
    {
        $pending_confirmations = StockService::getPendingStockConfirmation();

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
                    $pending_confirmation->approval_date = Carbon::now();

                    // $message = [];
                    if($pending_confirmation->approval_status == Constant::status_rejected) {
                        $pending_confirmation->approved_stock = 0;
                        // $message['subject'] = 'Konfirmasi Rejected';
                    }
                    else {
                        $pending_confirmation->approved_stock = intval($inputs['approved_stock']);
                        // $message['subject'] = 'Konfirmasi Approved';
                        if($pending_confirmation->approved_stock <= 0) {
                            return response()->json([
                                'status' => 'error',
                                'message_box' => '<b>WARNING</b> Stok approved minimum 1'
                            ]);
                        }
                    }
                    if($pending_confirmation->approved_stock > $pending_confirmation->stock) {
                        return response()->json([
                            'status' => 'error',
                            'message_box' => '<b>WARNING</b> Stok approved maksimum '. $pending_confirmation->stock
                        ]);
                    }
                    DB::beginTransaction();

                    $item = Item::where('item_id',$pending_confirmation->item_id)->first();
                    $branch = Branch::where('id', $pending_confirmation->branch_id)->first();
                    if($pending_confirmation->save()) {
                        if($pending_confirmation->approval_status == Constant::status_rejected) {
                            // $message['subject'] .= ' #'.$item->item_id.' '.$item->item_name.' Cabang: '.$branch->branch_name;
                            $param = [];
                            $param['item_id'] = $pending_confirmation->item_id;
                            $param['add_stock'] = $pending_confirmation->stock;
                            $param['modal_per_pcs'] = HelperService::maskMoney(intval($pending_confirmation->modal_pusat));
                            $param['branch'] = Branch::find($pending_confirmation->branch_id);
                            if(StockService::inputStockPusat($param, 2) == '') {
                                DB::commit();
                            }
                        }
                        else {
                            $pembukuan_pusat = $log = $branch_modal_log = [];
                            $pembukuan_pusat['branch_buyer'] = $log['branch_id'] = $pending_confirmation->branch_id;
                            $pembukuan_pusat['item_id'] = $log['item_id'] = $pending_confirmation->item_id;
                            //kalo approved tambahin branch stock
                            $branch_stock = BranchStock::where('item_id', $pending_confirmation->item_id)
                                                    ->where('branch_id', $pending_confirmation->branch_id)->first();
                            if($branch_stock==null) {
                                $branch_stock = new BranchStock();
                                $branch_stock->item_id = $pending_confirmation->item_id;
                                $branch_stock->branch_id = $pending_confirmation->branch_id;
                                $branch_stock->modal_per_pcs = $branch_stock->stock = 0;
                            }
                            $log['modal_per_pcs_before'] = $branch_stock->modal_per_pcs;
                            $log['stock_before'] = $branch_stock->stock;
                            $total_modal_before = $branch_stock->modal_per_pcs * $branch_stock->stock;

                            $log['stock_new_input'] = $stock_new_input = $pending_confirmation->approved_stock;
                            $log['modal_new_input'] = $modal_new_input = $pending_confirmation->modal_cabang;
                            $branch_modal_log['modal_value'] = $total_modal_new_input = $stock_new_input*$modal_new_input;

                            $log['stock_after'] = $branch_stock->stock = $branch_stock->stock + $pending_confirmation->approved_stock;
                            $total_modal_after = $total_modal_before + $total_modal_new_input;
                            $log['modal_per_pcs_after'] = $branch_stock->modal_per_pcs = $total_modal_after / $branch_stock->stock;

                            $branch_stock->save();

                            $log['supplied_by'] = $pending_confirmation->sender;
                            $log['approved_by'] = $pending_confirmation->approval;

                            $log_data = BranchStockLog::create($log);

                            $branch_modal_log['branch_id'] = $branch_stock->branch_id;
                            $branch_modal_log['information'] = 'Penambahan stok #'.$item->item_id.' '.$item->item_name.' sebanyak '.$stock_new_input.' @'. HelperService::maskMoney($modal_new_input);
                            $branch_modal_log['information'] .= ' | No. BSL: '.$log_data->id;
                            $branch_modal_log['modal_type'] = 1;
                            $bml = BranchModalLog::create($branch_modal_log);
                            if($item->item_type == Constant::type_id_sewa) {
                                $pb = [];
                                $pb['branch_id'] = $branch_stock->branch_id;
                                $pb['item_id']=$item->item_id;
                                $pb['qty_item'] = $stock_new_input;
                                $pb['modal_per_qty_item'] = $modal_new_input;
                                $pb['modal_total'] = $total_modal_new_input;
                                $pb['profit'] = 0 - $pb['modal_total'];
                                $pb['description'] = 'Pengadaan barang sewa';
                                //Pengadaan barang sewa
                                PembukuanBranch::create($pb);
                            }
                            $rejected = 0;
                            //kalo gak approved semua
                            if($pending_confirmation->approved_stock < $pending_confirmation->stock) {
                                $rejected = $pending_confirmation->stock-$pending_confirmation->approved_stock;
                            }

                            if($rejected > 0) {
                                // $message['subject'] .= ' '.$pending_confirmation->approved_stock.' dari '.$pending_confirmation->stock;
                                //balikin stok pusat
                                $param = [];
                                $param['item_id'] = $pending_confirmation->item_id;
                                $param['add_stock'] = $rejected;
                                $param['modal_per_pcs'] = HelperService::maskMoney(intval($pending_confirmation->modal_pusat));
                                $param['branch'] = Branch::find($pending_confirmation->branch_id);
                                StockService::inputStockPusat($param, 2);
                            }
                            else {
                                // $message['subject'] .= ' Semua';
                            }

                            $pembukuan_pusat['modal']= $pending_confirmation->modal_pusat * $pending_confirmation->approved_stock;
                            $pembukuan_pusat['turnover'] = $pending_confirmation->modal_cabang * $pending_confirmation->approved_stock;
                            $pembukuan_pusat['description'] = 'Penjualan #'.$item->item_id.' '.$item->item_name.' sebanyak '.$pending_confirmation->approved_stock.' No. TS: '.$pending_confirmation->id;
                            PembukuanPusat::create($pembukuan_pusat);
                            // dd($pembukuan_pusat);

                            // $message['subject'] .= ' #'.$item->item_id.' '.$item->item_name.' Cabang: '.$branch->branch_name;
                            // $pic_pusat = User::find($pending_confirmation->sender);
                            // $pic_cabang = User::find($pending_confirmation->approval);
                            // $message['content'] = 'Pic Pusat: '.$pic_pusat.'<br/>';
                            // $message['content'] = ''
                            // dd($message);
                            DB::commit();
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
