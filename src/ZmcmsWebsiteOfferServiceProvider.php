<?php
namespace Zmcms\WebsiteOffer;
use Illuminate\Support\ServiceProvider;
class ZmcmsWebsiteOfferServiceProvider extends ServiceProvider{

	public function register(){
		$this->app->make('Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteOfferController');
		$this->app->make('Zmcms\WebsiteOffer\Frontend\Controllers\ZmcmsWebsiteOfferController');
		require_once(__DIR__.'/helpers.php');
	}

	public function boot(){
		$this->loadMigrationsFrom(__DIR__.'/migrations');
		$this->publishes([
			__DIR__.'/config' => base_path('config/'),
			__DIR__.'/backend/css' => base_path('public/themes/backend/css/'),
			__DIR__.'/backend/js' => base_path('public/themes/backend/js/'),
			__DIR__.'/backend/views' => base_path('resources/views/themes/zmcms/backend'),
			__DIR__.'/frontend/css' => base_path('public/themes/frontend/css/'),
			__DIR__.'/frontend/js' => base_path('public/themes/frontend/js/'),
			__DIR__.'/frontend/views' => base_path('resources/views/themes/zmcms/frontend'),
		]);
	}

}
