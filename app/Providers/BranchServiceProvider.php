<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Branch;
use Sentinel;
class BranchServiceProvider extends ServiceProvider
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

    public static function addBranch($inputs)
    {
        $inputs['created_by'] = Sentinel::getUser()->id;
        return Branch::create($inputs);
    }
}
