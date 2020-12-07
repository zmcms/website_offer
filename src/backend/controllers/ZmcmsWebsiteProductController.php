<?php
namespace Zmcms\WebsiteOffer\Backend\Controllers;
use Illuminate\Http\Request;
use Zmcms\WebsiteOffer\Backend\Db\Queries as Q;
use Illuminate\Support\Facades\DB;
class ZmcmsWebsiteProductController extends \App\Http\Controllers\Controller
{
	public function zmcms_website_product_list(Request $request){
		$data = Q::products_list($paginate = 0, $order=[], $filter=[]);

		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_product_panel', compact('data'));
	}
	public function zmcms_website_product_list_refresh(Request $request){
		$data = Q::products_list($paginate = 0, $order=[], $filter=[]);

		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_product_list', compact('data'));
	}
	public function zmcms_website_offerfrm_create(){
		$data = [];
		$settings=[
			'title'	=> 'Tworzenie nowej oferty',
			'action' => 'create',
			'btnsave' => 'Zapisz',
		];
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_offer_frm', compact('data', 'settings'));	
	}
	public function zmcms_website_product_create(Request $request){
		$data = $request->all();
		if($data['action'] == 'update') return $this->zmcms_website_product_update($data);
		$d['ilustration']=null;
		if(strlen($data['data']['ilustration']) > 4) $d['ilustration'] = zmcms_image_save(
				base_path().DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.$data['data']['ilustration'],
				base_path().DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.Config('zmcms.frontend.theme_name').DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'store'.DIRECTORY_SEPARATOR.'wo'.DIRECTORY_SEPARATOR.'ilustrations',
					str_slug(date('Y-m-d').'-'.$data['names']['name']).'.jpg'
		);
		$data['data']['images_resized'] = json_encode($d);
		if($data['action'] == 'create') return json_encode(Q::product_create($data));
		$result = [
			'result' =>'ok',
			'code' =>'ok',
			'msg' => '<pre>'.print_r($request->all(), true).'</pre>',
		];
		return json_encode($result);
		// return print_r();
	}

/**
 *  
 Ciasto 36cm grube
Ciasto 32cm razowe
Ciasto 32cm razowe
Ciasto 22cm grube
Ciasto 22cm grube
Ciasto 36cm razowe
Ciasto 36cm razowe
Dodatek boczek
Dodatek boczek
Ciasto 32cm cienkie
Ciasto 32cm cienkie
Sos BBQ
Sos BBQ
Dodatek wołowina
Dodatek wołowina
Dodatek kurczak
Dodatek kurczak
Dodatek szynka szwarcwaldzka
Dodatek szynka szwarcwaldzka
Ciasto 22cm razowe
Ciasto 22cm razowe
Dodatek salami pepperoni
Dodatek salami pepperoni
Sos alpejski
Sos alpejski
Ciasto 22cm cienkie
Ciasto 22cm cienkie
Ciasto 32cm grube
Ciasto 32cm grube
Sos musztardowo - chrzanowy
Sos musztardowo - chrzanowy
Dodatek szynka
Dodatek szynka
 */

	public function zmcms_website_product_frm_edit($token){
		$data = Q::product_get($token, $langs_id='pl', $pageName = 'page', $pageNumber = null);
		$settings=[
			'title'	=> 'Aktualizacja oferty',
			'action' => 'update',
			'btnsave' => 'Zapisz',
		];
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_product_frm', compact('data', 'settings'));	
	}
	public function zmcms_website_product_update($data){
		$data['data']['images_resized'] = NULL;
		if(strlen($data['data']['ilustration']) > 4) $d['ilustration'] = zmcms_image_save(
				base_path().DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.$data['data']['ilustration'],
				base_path().DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.Config('zmcms.frontend.theme_name').DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'store'.DIRECTORY_SEPARATOR.'wo'.DIRECTORY_SEPARATOR.'ilustrations',
					str_slug(date('Y-m-d').'-'.$data['names']['name']).'.jpg'
		);
		$data['data']['images_resized'] = json_encode($d);
		return Q::product_update($data);
		$result = [
			'result' =>'ok',
			'code' =>'ok',
			'msg' => '<pre>UPDATE: '.print_r($data, true).'</pre>',
		];
		return json_encode($result);
	}
	public function zmcms_website_product_delete($token){
		$data = Q::product_get($token, $langs_id='pl', $pageName = 'page', $pageNumber = null);
		$resultset = json_decode(Q::product_delete($token));
		if(strlen($data['data']->images_resized)>0){

			foreach(json_decode(
				$data['data']->images_resized
			)->ilustration as $f)
				if(is_file(base_path().DIRECTORY_SEPARATOR.'public'.$f))unlink(base_path().DIRECTORY_SEPARATOR.'public'.$f);
		}
		return json_encode($resultset); 
	}
	public function zmcms_website_product_ajax_selector(Request $request, $offer_token, $type){
		$resultset = Q::products_list($paginate = 0, $order=[], $filter=[]);
		$data = [];
		
		foreach ($resultset as $r) {
			$data[]=[
				'in_offer'=>$r->offers_token,
				'token'=>$r->token,
				'offer_token'=>$offer_token,
				'type'=>$type,
				'name'=>$r->name,
				'slug'=>$r->slug,
				'images_resized'=>$r->images_resized,
				'code'=>$r->code,
				'ean13'=>$r->ean13,
				'ean128'=>$r->ean128,
			];
		}
		return $data;
		$result = [
			'result' =>'ok',
			'code' =>'ok',
			'msg' => '<pre>SELECTOR PRODUKTU: '.print_r($request->all(), true).'</pre>',
		];
		return ($result);
	}

	/**
	 * FORMULARZ EDYCJI PRODUKTU
	 */
	public function zmcms_website_product_frm($token){
		$data = Q::product_get($token);
		$settings=[
			'title'	=> 'Edycja produktu "'.$data['product']->name.'"',
			'action' => 'update',
			'btnsave' => 'Zapisz',
		];
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_product_frm' , compact('data', 'settings'));
		return '<pre>'.print_r(Q::product_get($token), true).'</pre>';
		return 'produkt '.$token;
	}
	public function zmcms_website_product_frm_create(){
		$data['product'] = new \stdClass() ;
		$data['product']->on_sale=null;
		$data['product']->supply_type=null;
		$data['product']->images_resized=null;
		$data['product']->compositions=null;
		$data['product']->name=null;
		$data['product']->slug=null;
		$data['compositions']=null;
		
		
		
		$settings=[
			'title'	=> 'Nowy produkt',
			'action' => 'create',
			'btnsave' => 'Zapisz',
		];
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_product_frm' , compact('data', 'settings'));
	}
	public function zmcms_website_product_composition_tree($token){
		$data = Q::product_composition_get($token);
		$arr=[];
		foreach($data as $d){
			$arr[$d->token]=$d;

		}
		$product_token= $token;
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_product_composition_tree' , compact('data', 'product_token'));
	}

	public function zmcms_website_product_composition($token){
		$d = Q::product_composition_get($token);
		$data = Q::product_composition_tree($d, $token);
		$product_token= $token;
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_product_composition_tree' , compact('data', 'product_token'));
		return '<pre>'.print_r($tree, true).'</pre>';
	}

	public function zmcms_website_product_save(Request $request){
		$data= $request->all();
		 // $result = [
		 // 'result' =>'ok',
		 // 'code' =>'ok',
		 // 'msg' => '<pre>ZAPISYWANIE PRODUKTU: '.print_r($request->all(), true).'</pre>',
		 // ];
		 // return json_encode($result);
		$d['icon'] = $d['ilustration'] = $d['og_image'] = null;
		if(strlen($data['data']['ilustration']) > 4) $d['ilustration'] = zmcms_image_save(
				base_path().DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.$data['data']['ilustration'],
				base_path().DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.Config('zmcms.frontend.theme_name').DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'store'.DIRECTORY_SEPARATOR.'wp'.DIRECTORY_SEPARATOR.'ilustrations',
				str_slug(date('Ymdhis').'-'.$data['names']['name']).'.jpg'
		);
		if(strlen($data['names']['og_image']) > 4) $d['og_image'] = zmcms_image_save(
				base_path().DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.$data['names']['og_image'],
				base_path().DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.Config('zmcms.frontend.theme_name').DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'store'.DIRECTORY_SEPARATOR.'wp'.DIRECTORY_SEPARATOR.'og',
				str_slug(date('Ymdhis').'-'.$data['names']['name']).'.jpg'
		);
		$data['data']['images_resized'] = json_encode($d);
		switch($data['action']){
			case 'update': {return Q::product_update($data); break;}
			case 'create': {return Q::product_create($data); break;}
		}
		$result = [
			'result' =>'ok',
			'code' =>'ok',
			'msg' => '<pre>ZAPISYWANIE PRODUKTU: '.print_r($request->all(), true).'</pre>',
		];
		return json_encode($result);
	}
	public function zmcms_website_product_composition_frm_add($token){
		$data = Q::product_get($token);
		// $data = Q::product_composition_get($token, $array = true);
		// $lst=Q::products_list($paginate = 0, $order=[], $filter=[]);
		$lst = Q::product_completation_linker_list($token, $paginate = 20, $order=[], $filter=[]);
		$arr=[];

		foreach($data['compositions'] as $r){
			$arr[]=$r['t'];
		}
		$compositions_selected=$arr;
		$settings=[
			'title'	=> 'Edycja produktu "'.$data['product']->name.'"',
			'action' => 'update',
			'btnsave' => 'Zapisz',
		];
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_product_composition_add' , compact('data', 'settings', 'compositions_selected', 'lst'));
		
		return 'nowy składnik kompozycji produktu: '.$token;
	}
	public function zmcms_website_product_composition_frm_add_lst($token = null){
		$data = Q::product_get($token);
		$lst = Q::product_completation_linker_list($token, $paginate = 20, $order=[], $filter=[], $pageName='page');
		$arr=[];
		foreach($data['compositions'] as $r){
			$arr[]=$r['t'];
		}
		$compositions_selected=$arr;
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_product_composition_add_lst' , compact('lst', 'compositions_selected'));
	}
	public function zmcms_website__delete_from_product_composition(Request $request){
		$data = $request->all();
		return Q::delete_composition_part($data['rid']);
		// product_token
		// composition_token
		// rid
		if(isset($data['product_token']))
			return $this->zmcms_website_product_composition_frm_add_lst($data['product_token']);
		$result = [
			'result' =>'ok',
			'code' =>'ok',
			'msg' => '<pre>KOMPOZYCJA: '.print_r($request->all(), true).'</pre>',
		];
		return json_encode($result);
	}
	public function zmcms_website__add_to_product_composition(Request $request){
		$data = $request->all();
		Q::create_composition_part($data);
		// $result = [
		// 	'result' =>'ok',
		// 	'code' =>'ok',
		// 	'msg' => '<pre>KOMPOZYCJA: '.print_r($request->all(), true).'</pre>',
		// ];
		// return json_encode($result);
		return $this->zmcms_website_product_composition_frm_add_lst($data['product_token']);
		$result = [
			'result' =>'ok',
			'code' =>'ok',
			'msg' => '<pre>KOMPOZYCJA: '.print_r($request->all(), true).'</pre>',
		];
		return json_encode($result);
	}
	public function zmcms_website_product_composition_full_view($token){
		$data = Q::product_get($token);
		$settings=[
			'title'	=> ___('Złożenie produktu').' "'.($data['product']->name).'"',
			'action' => 'update',
			'btnsave' => 'Zapisz',
		];
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_product_composition_full_view' , compact('data', 'settings'));
	}

	public function zmcms_website_product_composition_full_view_list_refresh($token){
		$d = Q::product_get($token);
		$product = $d['product'];
		$data = $d['versions'];
		$dict = $d['dict'];
		// $d = Q::product_composition_get($token);
		// $data['compositions'] = Q::product_composition_tree($d, $token);
		$settings=[
			'title'	=> ___('Złożenie produktu').' "'.($product->name).'"',
			'action' => 'update',
			'btnsave' => 'Zapisz',
		];
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_product_composition_full_view_list' , compact('data', 'settings', 'product', 'dict'));
	}
	public function zmcms_website_product_composition_update(Request $x){
		Q::update_composition_part($x->all());
		$result = [
			'result' =>'ok',
			'code' =>'ok',
			'msg' => '<pre>KOMPOZYCJA: '.print_r($x->all(), true).'</pre>',
		];
		return json_encode($result);
	}
	public function copletations_dict_frm(){
		$data = Q::completations_dict($paginate = 2, $order=[], $filter=[], $format=null);
		$settings=[
			'title'	=> ___('TEST'),
			'action' => 'update',
			'btnsave' => 'Zapisz',
		];
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.copletations_dict_frm' , compact('data', 'settings'));
		return __METHOD__;
	}
	public function completation_group_dict_table(){
		$data = Q::completations_dict($paginate = 2, $order=[], $filter=[], $format=null);
		$settings=[
			'title'	=> ___('TEST'),
			'action' => 'update',
			'btnsave' => 'Zapisz',
		];
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.completation_group_dict_table' , compact('data', 'settings'));
	}
	public function draganddroptest(){
		$data = [];
		$settings=[
			'title'	=> ___('TEST'),
			'action' => 'update',
			'btnsave' => 'Zapisz',
		];
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.draganddroptest' , compact('data', 'settings'));
	}



	/**
	 * DODAWANIE NOWEJ WERSJI PRODUKTU
	 */
	public function zmcms_website_product_version_create_frm($token, $p=null){
		// $data = Q::completations_dict($paginate = 2, $order=[], $filter=[], $format=null);
		$data= Q::product_get($token);
		// return 'xx-'.$p.'-xx';
		$dict = $data['dict'];
		if($p!=null){
			$data['p'] = $p;
		}
		$settings=[
			'title'	=> ___('Tworzenie wersji produktu'),
			'action' => 'create',
			'btnsave' => 'Zapisz',
		];
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_product_version_frm' , compact('data', 'dict', 'settings'));
	}

	public function zmcms_website_product_version_create(Request $request){
		$result = [
			'result' =>'ok',
			'code' =>'ok',
			'msg' => '<pre>Wersja: '.print_r($request->all(), true).'</pre>',
		];
		// return json_encode([
			// 'result'	=>	'ok',
			// 'code'		=>	'ok',
			// 'msg' 		=>	___('<pre>'.print_r($request->all(), true).'</pre>'),
		// ]);
		return $result = Q::create_product_version($request);
		return json_encode($result);
	}
}


/**
 * ok:
Wersja: Array
(
    [token] => 
    [name] => 
    [sort] => 
    [group] => 
    [default] => 
    [price_affected] => 
    [default_q] => 
    [max_q] => 
    [price_brut] => 
)
 */