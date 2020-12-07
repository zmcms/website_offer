<?php
$prefix = Config('zmcms.main.backend_prefix');
Route::middleware(['BackendUser'])->group(function () use($prefix){
	Route::get($prefix.'/zmcms_website_offer_list', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteOfferController@zmcms_website_offer_list');
	Route::get($prefix.'/zmcms_website_offer_list_refresh', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteOfferController@zmcms_website_offer_list_refresh');
	
	Route::get($prefix.'/website/offer/frm/create', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteOfferController@zmcms_website_offer_frm_create');
	Route::post($prefix.'/website/offer/create', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteOfferController@zmcms_website_offer_create');
	Route::get($prefix.'/website/offer/edit/{token}', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteOfferController@zmcms_website_offer_frm_edit');
	Route::get($prefix.'/website/offer/delete/{token}', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteOfferController@zmcms_website_offer_delete');
	Route::get($prefix.'/website/offer/object_add_frm/{token}/{type?}', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteOfferController@object_add_frm');
	Route::get($prefix.'/website/offer/get_offer_selection/{token}/{data_only?}',
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteOfferController@zmcs_offer_selection');
	Route::post($prefix.'/website/offer/objectselection/toggle', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteOfferController@toggle_connection');
	Route::get($prefix.'/website/offer/objectselection/{object_type}', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteOfferController@get_object_list');
	//PRODUKTY
	
	Route::get($prefix.'/zmcms_website_roduct_list', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@zmcms_website_product_list');
	Route::get($prefix.'/zmcms_website_product_list_refresh', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@zmcms_website_product_list_refresh');

	Route::get($prefix.'/website/product/show/{token}', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@zmcms_website_product_frm');
	Route::get($prefix.'/website/product/frm_create', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@zmcms_website_product_frm_create');

	Route::any($prefix.'/website/product/version_create_frm/{token}', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@zmcms_website_product_version_create_frm');
	Route::post($prefix.'/website/product/version_create', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@zmcms_website_product_version_create');
	Route::get(
		$prefix.'/website_product/composition/create_child/{token}/{p}', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@zmcms_website_product_version_create_frm'
	);
	

	
	Route::get($prefix.'/website/product/composition/{token}', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@zmcms_website_product_composition');
	Route::post($prefix.'/website/product/save', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@zmcms_website_product_save');
	Route::any($prefix.'/website_product/composition_add/ajax/list/{token}',
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@zmcms_website_product_composition_frm_add_lst');
	Route::get($prefix.'/website/product/composition_add/{token}', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@zmcms_website_product_composition_frm_add');
	Route::post($prefix.'/website_product/composition/delete_from_product', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@zmcms_website__delete_from_product_composition');
	Route::post($prefix.'/website_product/composition/add_to_product', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@zmcms_website__add_to_product_composition');
	
	Route::get($prefix.'/zmcms_website_product_composition/{token}', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@zmcms_website_product_composition');

	Route::get($prefix.'/website_product/composition/add_to_product/full_view/{token}', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@zmcms_website_product_composition_full_view');

	/**
	 * 
	 */
	Route::get($prefix.'/website_product/composition/add_to_product/full_view/refresh/{token}', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@zmcms_website_product_composition_full_view_list_refresh');

	

	Route::post(
		$prefix.'/website/product/completation/update', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@zmcms_website_product_composition_update');
	Route::get(
		$prefix.'/draganddroptest', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@draganddroptest'
	);
	Route::get(
		$prefix.'/website/product/copletations/dict/frm', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@copletations_dict_frm'
	);
	Route::get(
		$prefix.'/website/product/copletations/dict/table', 
		'Zmcms\WebsiteOffer\Backend\Controllers\ZmcmsWebsiteProductController@completation_group_dict_table'
	);
});



