
$(document).ready(function(){
	var backend_prefix = $('meta[name="backend-prefix"]').attr('content');
	$("#btn_zwol").on('click', function(e){
		alert('offer');
		// location.href = "/"+backend_prefix+"/website/offer/zmcms_website_offer_list";
		return false;
	});
	$("#btn_zwopl").on('click', function(e){
		alert('products');
		// location.href = "/"+backend_prefix+"/website/offer/zmcms_website_products_list";
		return false;
	});
	$("#btn_zwopll").on('click', function(e){
		alert('pricelists');
		// location.href = "/"+backend_prefix+"/website/offer/zmcms_website_pricelists_list";
		return false;
	});
	$("#btn_zwool").on('click', function(e){
		alert('orders');
		// location.href = "/"+backend_prefix+"/website/offer/zmcms_website_orders_list";
		return false;
	});
});