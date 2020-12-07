<?php
Route::middleware(['FrontendUser'])->group(function () {
	Route::post(
		'website/frontend/product/add_to_cart', 
		'Zmcms\WebsiteOffer\Frontend\Controllers\ZmcmsWebsiteProductController@website_frontend_product_add_to_cart'
	);
	Route::get('/ajax_choose_product_version_frm/{token}', 'Zmcms\WebsiteOffer\Frontend\Controllers\ZmcmsWebsiteProductController@ajax_choose_product_version_frm');
	Route::get('/ajax_add_to_cart/{token}', 'Zmcms\WebsiteOffer\Frontend\Controllers\ZmcmsWebsiteProductController@ajax_add_to_cart');
});
// echo 'offer1<br />';