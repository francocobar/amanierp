<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use App\Member;
use App\Branch;

class ExportExcelController extends Controller
{
    public function __construct()
    {
        $this->middleware('authv2');
        $this->middleware('checkrole_sa_manager');
    }

    function getMemberByBranch($branch_id)
    {
        $branch = null;
        if($branch_id){
            $branch = Branch::find($branch_id);
        }
        if(!$branch) {
            abort(404);
        }
        $members_show_excel[] = array('NO','NAMA','NOMOR HP', 'MEMBER ID', 'CABANG');
        $members = Member::whereNotNull('member_id')->where('branch_id', $branch->id)
            ->orderBy('full_name')->get();
        // dd($members->branch);

        $no = 0;
        foreach ($members as $key => $member) {
            $no++;
            $members_show_excel[] = array($no, $member->full_name,$member->phone,
                $member->member_id, $branch->branch_name);
        }
        Excel::create('DATA MEMBER '. $branch->branch_name, function($excel) use ($members_show_excel)  {
            // Set the title
            $excel->setTitle('AMANIE');

            // Chain the setters
            $excel->setCreator('AMANIE')
                  ->setCompany('AMANIE');

            // Call them separately
            $excel->setDescription('data member');


            $excel->sheet('Data Member', function($sheet) use ($members_show_excel) {
                $sheet->cells('A1:E1', function($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->fromArray($members_show_excel, null, 'A1', false, false);
            });
        })->download('xlsx');
    }
}
