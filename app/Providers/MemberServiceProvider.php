<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Sentinel;
use HelperService;
use App\Member;
use App\Services\PayUService\Exception;
use App\Log;
use DB;

class MemberServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public static function addMember($inputs)
    {
        $inputs['created_by'] = Sentinel::getUSer()->id;
        if(!isset($inputs['member_id']) || empty(trim($inputs['member_id']))) {
            $prefix_member_id = HelperService::getPrefixMemberId($inputs['branch_id']);

            $number_id = 1;

            $last_member = Member::where('branch_id', $inputs['branch_id'])
                                        ->where('member_id','like',$prefix_member_id.'%')
                                        ->orderBy('created_at', 'desc')
                                        ->withTrashed()
                                        ->first();

            if($last_member != null) {
                $last_number_id = str_replace($prefix_member_id,'',$last_member->member_id);
                $number_id = $last_number_id+1;
            }


            $inputs['member_id'] = $prefix_member_id.sprintf("%05d", $number_id);
        }
        else {
            $inputs['member_id'] = trim(strtoupper($inputs['member_id']));
            if(strpos($inputs['member_id'], ' ') == false) {
                return ['error' => 'Member ID lama harus mengandung spasi! Contoh <b>A 1234</b>!'];
            }
            if(strpos($inputs['member_id'], '  ') == true) {
                return ['error' => 'Member ID lama hanya mengandung satu spasi! Contoh <b>B 1234</b>'];
            }
            $member_id_check = explode(' ', $inputs['member_id']);
            if(count($member_id_check)!=2) {
                return ['error' => 'Format ID lama salah, Contoh yang benar C 1234, A 0004'];
            }
            if(!ctype_alpha($member_id_check[0]) || !is_numeric($member_id_check[1]) || strlen($member_id_check[0]) != 1) {
                return ['error' => 'Format ID lama salah, Contoh yang benar C 1234, A 0004'];
            }
            if(strlen($member_id_check[1]) != 4) {
                return ['error' => 'Format ID lama salah, setelah huruf wajib 4 digit Angka'];
            }

            $flag = Member::where('member_id', $inputs['member_id'])->first();
            if($flag) {
                return ['error' => 'Member ID sudah pernah didaftarkan!'];
            }
        }
        $inputs['full_name'] = ucwords($inputs['full_name']);
        return Member::create($inputs);
    }

    public static function removeMember($inputs)
    {
        DB::beginTransaction();
        try {
            $member_id = trim($inputs['member_id']);
            $member = Member::where('member_id', $member_id)->first();

            $log = new Log();
            $log->log_text = trim($inputs['log']);
            $log->log_by = Sentinel::getUser()->id;
            $log->log_for = 'remove member '. $member_id;
            $log->save();

            $member->update(['deleted_by'=>Sentinel::getUser()->id,
                    'deleted_reason' => $log->id
                ]);
            $member->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return false;
        }


        return true;
    }
}
