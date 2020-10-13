<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\Datatable;

class DatatableServiceProvider extends ServiceProvider {
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('dataTable', function(){
            return new DataTable;
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        
    }
}
