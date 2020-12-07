<?php
namespace Zmcms\WebsiteOffer\Frontend\Controllers;
use Illuminate\Http\Request;
use Zmcms\WebsiteOffer\Frontend\Db\Queries as Q;
use Zmcms\WebsiteNavigations\Frontend\Model\WebsiteNavigationJoined as QN;
class ZmcmsWebsiteProductController extends \App\Http\Controllers\Controller
{
	/**
	 * URUCHOMIENIE KLASY "Z CONFIGA" 
	 */
	public function run($token_nav = null, $token_obj = null, $type = null){
		$data['navigation'] = QN::get_navigation($token_nav);
		$head = $this->head_data($data['navigation']);
		$data['products']=$resultset = Q::products_list($token_nav, null, 10);
		if($token_nav == $token_obj){
			return view('themes.'.(Config('zmcms.frontend.theme_name') ?? 'zmcms').'.frontend.zmcms_website_products', compact('head', 'data'));
		}else{
			$data['product'] = Q::get_product($data['products'][0]->token);
			$data['product_composition'] = Q::product_composition($data['products'][0]->token);
			$head = $this->head_data($data['product']['product']);
			return view('themes.'.(Config('zmcms.frontend.theme_name') ?? 'zmcms').'.frontend.zmcms_website_product_selected', compact('head', 'data'));	
		}
		
	}
	/**
	 * POMOCNICZA
	 */
	public function head_data($data){
			$head['title'] = $data->name;
			$head['keywords'] = $data->meta_keywords;
			$head['description'] = $data->meta_description;
			$head['canonical'] = null;
			$head['og:title'] = $data->og_title ?? $data->name;
			$head['og:type'] = $data->og_type;
			$head['og:url'] = $data->og_url;
			$head['og:image'] = $data->og_image;
			$head['og:description'] = $data->og_description;
			$head['og:locale'] = 'pl';
			$head['language'] = 'pl';

			return $head;
	}
	/**
	 *  DODAWANIE PRODUKTU DO KOSZYKA
	 */
	public function website_frontend_product_add_to_cart(Request $request){
		return '<pre>'.print_r($request->all(), true).'</pre>';
	}
	public function ajax_add_to_cart($token){
		return view('themes.'.(Config('zmcms.frontend.theme_name') ?? 'zmcms').'.frontend.ajax_product_cart_add', compact('data'));
		return '2'.__METHOD__;
	}
	/**
	 * formularz wyboru WERSJI PRODUKTU
	 */
	public function ajax_choose_product_version_frm($token){
		$data['products'] = Q::get_product($token);
		$data['products_composition'] = Q::product_composition($token);
		return view('themes.'.(Config('zmcms.frontend.theme_name') ?? 'zmcms').'.frontend.ajax_choose_product_version_frm', compact('data'));
		return '<pre>'.print_r($data, true).'</pre>';
	}	
}
