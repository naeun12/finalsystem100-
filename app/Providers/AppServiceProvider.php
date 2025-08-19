<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\tenant\tenantModel;
use App\Models\landlord\landlordModel;
    use Illuminate\Database\Eloquent\Relations\Relation;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // public function boot(): void
    // {
    //     View::composer('*', function ($view) {
    //         $view->with('landlord_id', session('landlord_id'));
    //     });
    // }


public function boot()
{
    Relation::morphMap([
        'tenant' => tenantModel::class,
        'landlord' => landlordModel::class,
    ]);
}

}
