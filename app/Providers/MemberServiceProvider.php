<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Sentinel;
use HelperService;
use App\Member;

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
                                        ->first();

            if($last_member != null) {
                $last_number_id = str_replace($prefix_member_id,'',$last_member->member_id);
                $number_id = $last_number_id+1;
            }


            $inputs['member_id'] = $prefix_member_id.sprintf("%05d", $number_id);
        }
        else {
            $inputs['member_id'] = trim($inputs['member_id']);
            $flag = Member::where('member_id', $inputs['member_id'])->first();
            if($flag) {
                return ['error' => 'Member ID sudah pernah didaftarkan!'];
            }
        }

        return Member::create($inputs);
    }
}
