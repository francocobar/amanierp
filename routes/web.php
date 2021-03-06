<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/info', function(){
    return phpinfo();
});
Route::get('/testing', 'UserController@testing');
Route::get('/', function () {
    return redirect('/login');
});
Route::get('/superadmin', function () {
    return view('superadmin');
});

Route::get('/add-branch', 'BranchController@addBranch')->name('add.branch');
Route::post('/add-branch.do', 'BranchController@addBranchDo')->name('add.branch.do');
Route::get('/branches/{page}', 'BranchController@getBranches')->name('get.branches');

Route::get('/add-employee', 'EmployeeController@addEmployee')->name('add.employee');
Route::post('/add-employee.do', 'EmployeeController@addEmployeeDo')->name('add.employee.do');
Route::get('/my-salary', 'EmployeeController@mySalary')->name('my.salary');

Route::get('/add-member', 'MemberController@addMember')->name('add.member');
Route::get('/edit-member/{member_id}', 'MemberController@editMember')->name('edit.member');
Route::post('/edit-member.do', 'MemberController@editMemberDo')->name('edit.member.do');
Route::post('/add-member.do', 'MemberController@addMemberDo')->name('add.member.do');
Route::post('/remove-member.do', 'MemberController@removeMemberDo')->name('remove.member.do');
Route::get('/members/{page}', 'MemberController@getMembers')->name('get.members');
Route::get('/export-members', 'MemberController@exportMembers')->name('export.members');
Route::get('/do-export-members/{branch_id}', 'ExportExcelController@getMemberByBranch')->name('export.members.do');
Route::post('/members', 'MemberController@getMembersByAjax')->name('get.members.by.ajax');

Route::get('/add-item', 'ItemController@addItem')->name('add.item');
Route::post('/update-item', 'ItemController@updateItem')->name('update.item');
// Route::get('/edit-item/{item_id_encrypted}/{url_ref}', 'ItemController@editItem')->name('edit.item');
// Route::post('/update-item/{item_id_encrypted}', 'ItemController@updateItem')->name('update.item');
Route::post('/add-item.do', 'ItemController@addItemDo')->name('add.item.do');
Route::post('/items', 'ItemController@getItemsByAjax')->name('get.items.by.ajax');

Route::get('/items-produk/{page?}', 'ItemController@getItemsProduk')->name('get.items.produk');
Route::get('/item-produk/{item_id}', 'ItemController@getItemProdukById')->name('detail.item.produk');
Route::get('/items-jasa/{page?}', 'ItemController@getItemsJasa')->name('get.items.jasa');
Route::get('/item-jasa/{item_id}', 'ItemController@getItemJasaById')->name('detail.item.jasa');
Route::get('/configure-jasa/{item_id}', 'ItemController@getJasaConfiguration')->name('configure.item.jasa');
Route::get('/items-sewa/{page?}', 'ItemController@getItemsSewa')->name('get.items.sewa');
Route::get('/item-sewa/{item_id}', 'ItemController@getItemSewaById')->name('detail.item.sewa');
Route::get('/items-paket/{page?}', 'ItemController@getItemsPaket')->name('get.items.paket');
Route::get('/item-paket/{item_id}', 'ItemController@getItemPaketById')->name('detail.item.paket');
Route::get('/configure-paket/{item_id}', 'ItemController@getPaketConfiguration')->name('configure.item.paket');
// Route::get('/item-sewa/{item_id}', 'ItemController@getItemSewaById')->name('detail.item.sewa');

Route::get('/input-stock-pusat/{item_id}','ItemController@inputStock')->name('input.stock.item');
Route::post('/input-stock-pusat/{item_id}.do','ItemController@inputStockDo')->name('input.stock.item.do');
Route::get('/supply-branch/{item_id}.{branch_id}','ItemController@supplyBranch')->name('supply.stock.item');
Route::post('/supply-branch/{item_id}.{branch_id}.do','ItemController@supplyBranchDo')->name('supply.stock.item.do');
Route::get('/pending-confirmation', 'ItemController@getPendingConfirmations')->name('get.item.pending.confirmations');
Route::get('/approved-confirmation', 'ItemController@getApprovedConfirmations')->name('get.item.approved.confirmations');
Route::get('/rejected-confirmation', 'ItemController@getRejectedConfirmations')->name('get.item.rejected.confirmations');
Route::post('/confirmation.do', 'ItemController@doConfirmPendingConfirmation')->name('confirm.pending.confirmation');

Route::get('/delete-configuration-jasa/{configuration_id}', 'ItemController@deleteJasaConfiguration')->name('delete.configuration.item.jasa');
Route::post('/update-configuration-jasa/{configuration_id}', 'ItemController@updateJasaConfiguration')->name('update.configuration.item.jasa');
Route::get('/delete-configuration-paket/{configuration_id}', 'ItemController@deletePaketConfiguration')->name('delete.configuration.item.paket');
Route::post('/update-configuration-paket/{configuration_id}', 'ItemController@updatePaketConfiguration')->name('update.configuration.item.paket');

Route::get('/employees/unset-incentives', 'EmployeeController@unsetIncetives')->name('unset.incentives.employees');
Route::post('/employees/set-incentive.do', 'EmployeeController@doSetIncetives')->name('set.incentives.employee.do');
Route::post('/employees/update-work-since.do', 'EmployeeController@doUpdateWorkSince')->name('update.employee.work-since.do');
Route::get('/employees/incentives', 'EmployeeController@getEmployeesIncentives')->name('get.employees.incentives');
Route::get('/employees/{page}', 'EmployeeController@getEmployees')->name('get.employees');
Route::get('/employee/{employee_id}.{user_id}', 'EmployeeController@getEmployeeAndUser')->name('get.employee.user');
Route::post('/employee/change-role/{employee_id}.{user_id}', 'EmployeeController@changeRole')->name('role.employee.user');
Route::post('/employee/give-new-password/{employee_id?}.{user_id?}', 'EmployeeController@giveNewPassword')->name('password.employee.user');
Route::post('/employee/new_salary/{employee_id}.{user_id}', 'EmployeeController@setNewSalary')->name('salary.employee.user');

Route::get('/delete-employee-salary/{employee_salary_id}', 'EmployeeController@deleteSalary')->name('delete.salary');
Route::get('create-first-user', 'UserController@createFirstUser')->name('create.first.user');

Route::get('/login', 'UserController@login')->name('login');
Route::post('/login.do', 'UserController@loginDo')->name('login.do');
Route::get('/dashboard', 'UserController@dashboard')->name('dashboard');

Route::get('/cashier-v2', 'TransactionController@getCashier2')->name('get.cashier.v2');
Route::post('/add-trans', 'TransController@addTrans')->name('do.cashier.add-transaction');
// Route::get('/employee-incentives', 'EmployeeController@incentiveList');
Route::get('/employee-incentive/{b}/{key?}', 'TransController@employeeIncentive')->name('do.cashier.employee-incentive');
Route::get('/pending-trans/{b}/{key?}', 'TransController@pendingTrans')->name('do.cashier.check-pending');
Route::post('/update-pending-trans', 'TransController@updateQtyPendingTrans')->name('do.cashier.update-pending');
Route::get('/ongoing-trans/{trans_id}', 'TransController@ongoingTrans')->name('get.cashier.ongoing');
Route::post('/ongoing-trans-next-step', 'TransController@doNextStep')->name('do.cashier.next-step');
Route::post('/ongoing-trans-last-step', 'TransController@doLastStep')->name('do.cashier.last-step');
Route::post('/add-item-ongoing-trans', 'TransController@addItemTrans')->name('do.cashier.ongoing-add-item');
Route::post('/update-item-ongoing-trans', 'TransController@updateItemTrans')->name('do.cashier.ongoing-update-item');
Route::post('/change-status-trans', 'TransController@changeStatus')->name('do.changestatus');


Route::get('/cashier', 'TransactionController@getCashier')->name('get.cashier');
Route::get('/cashier-next-payment', 'TransactionController@getCashierPelunasan')->name('get.cashier.next-payment');
Route::post('/cashier-next-payment.do/{invoice_id}', 'TransactionController@doPelunasan')->name('do.cashier.next-payment');
Route::get('/items/{branch}', 'TransactionController@getItems')->name('get.items.cashier');
Route::get('/pic', 'TransactionController@getPic')->name('get.pic.cashier');
Route::get('/members', 'TransactionController@getMembers')->name('get.members.cashier');
Route::get('/branches-for-cashier', 'TransactionController@getBranchesForTrans')->name('get.branchesfor.cashier');
Route::get('/branches-sewa/{item_id?}.{date_to_rent?}', 'TransactionController@getBranches')->name('get.branches.cashier');
Route::post('/transaction.do', 'TransactionController@doTransaction')->name('do.transaction');



// Route::get('/cashier',function(){
//     // return request();
//     $ipAddress = '';
//
//         // Check for X-Forwarded-For headers and use those if found
//         if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ('' !== trim($_SERVER['HTTP_X_FORWARDED_FOR']))) {
//             $ipAddress = trim($_SERVER['HTTP_X_FORWARDED_FOR']);
//         } else {
//             if (isset($_SERVER['REMOTE_ADDR']) && ('' !== trim($_SERVER['REMOTE_ADDR']))) {
//                 $ipAddress = trim($_SERVER['REMOTE_ADDR']);
//             }
//         }
//     return $ipAddress;
// });
// Route::get('/cashier2',function(){
//     return redirect('/cashier');
// });

Route::get('/add-discount-vouchers', 'VoucherController@addVoucher')->name('add.discount.vouchers');
Route::get('/discount-vouchers/{page?}', 'VoucherController@getDiscountVouchers')->name('get.discount.vouchers');
Route::post('/add-discount-vouchers.do', 'VoucherController@addVoucherDo')->name('add.voucher.do');
Route::get('/validates-voucher', 'VoucherController@validateVoucher')->name('validate.voucher');

Route::get('logout', 'UserController@logout')->name('logout');

Route::get('/trans-report/{period}/{spesific?}/{branch?}', 'Report2Controller@getSalesReport')->name('get.sales.report');
Route::get('/top-members/{spesific?}/{branch?}', 'Report2Controller@topMembers')->name('topmembers.report');
Route::get('/top-items/{branch?}/{from}/{to}', 'Report2Controller@topItems')->name('topitems.report');
Route::get('/sales-details/{branch?}/{from}/{to}', 'Report2Controller@salesDetails')->name('salesdetails.report');
Route::post('/get-sales-details', 'Report2Controller@getSalesdetailsByAjax')->name('get.salesdetails.by.ajax');

Route::get('/pb', 'ReportController@pembukuanBranch')->name('pb.report');
Route::get('/pb/modal-note/{id}', 'ReportController@pembukuanBranchById')->name('pb.modal.note');
Route::get('/sales-report-pusat/{period}/{spesific?}/{branch?}', 'ReportController@getSalesReportPusat')->name('get.sales.report.pusat');

Route::get('/search-invoices', 'TransactionController@searchInvoice')->name('search.invoice.cashier');
Route::get('/invoice','TransactionController@getInvoice')->name('get.invoice.cashier');
Route::get('claim.do','TransactionController@doClaim')->name('do.claim');
Route::get('/renting-datas/{header_id}', 'RentingController@rentingDatas')->name('renting.by.invoice.casier');
Route::get('/renting-datas-timeline', 'RentingController@rentingDatasByTime')->name('renting.by.time.casier');
Route::get('/renting-datas/{action}/{reting_data_id}', 'RentingController@changeStatusRentingData')->name('change.status.renting');
Route::get('/monitoring/trans', 'MonitoringController@trans')->name('monitoring.trans');
