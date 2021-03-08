<?php
namespace Tridi\Tripay\Payment;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class ServiceProvider extends LaravelServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap the application services.
     * 
     * @return void
     */

     public function boot()
     {
         $this->publishes([
            dirname(__DIR__).'/config.php'=>config_path('tripay.php'),
         ]);
     }

     /**
      * Register the application services
      *
      *@return void
      */
      public function register()
      {
          $this->app->singleton(\Payment::class,function($app){
            return new Payment();
          });
      }

      /**
       * Get the services provider by the provider.
       * 
       * @return array
       */
      public function provides()
      {
          return [\Payment::class];
      }
}
?>