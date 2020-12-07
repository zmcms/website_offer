<?php
namespace Zmcms\WebsiteOffer\Backend\Controllers;
use Illuminate\Http\Request;
use Zmcms\WebsiteOffer\Backend\Db\Queries as Q;
class ZmcmsWebsiteOfferController extends \App\Http\Controllers\Controller
{
	public function zmcms_website_offer_list(Request $request){
		$data = Q::offers_list($paginate = 0, $order=[], $filter=[]);

		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_offer_panel', compact('data'));
	}
	public function zmcms_website_offer_list_refresh(){
		$data = Q::offers_list($paginate = 0, $order=[], $filter=[]);
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_offer_list', compact('data'));
	}
	public function zmcms_website_offer_frm_create(){
		$data = [];
		$settings=[
			'title'	=> 'Tworzenie nowej oferty',
			'action' => 'create',
			'btnsave' => 'Zapisz',
		];
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_offer_frm', compact('data', 'settings'));	
	}
	public function zmcms_website_offer_create(Request $request){
		$data = $request->all();
		if($data['action'] == 'update') return $this->zmcms_website_offer_update($data);
		$d['ilustration']=null;
		if(strlen($data['data']['ilustration']) > 4) $d['ilustration'] = zmcms_image_save(
				base_path().DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.$data['data']['ilustration'],
				base_path().DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.Config('zmcms.frontend.theme_name').DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'store'.DIRECTORY_SEPARATOR.'wo'.DIRECTORY_SEPARATOR.'ilustrations',
					str_slug(date('Y-m-d').'-'.$data['names']['name']).'.jpg'
		);
		$data['data']['images_resized'] = json_encode($d);
		if($data['action'] == 'create') return Q::offer_create($data);
		$result = [
			'result' =>'ok',
			'code' =>'ok',
			'msg' => '<pre>'.print_r($request->all(), true).'</pre>',
		];
		return json_encode($result);
		// return print_r();
	}

	public function zmcms_website_offer_frm_edit($token){
		$data = Q::offer_get($token, $langs_id='pl', $pageName = 'page', $pageNumber = null);
		$offer_selection = Q::offer_get_sellection($token, $data_only=true);
		$settings=[
			'title'	=> 'Aktualizacja oferty',
			'action' => 'update',
			'btnsave' => 'Zapisz',
		];
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_offer_frm', compact('data', 'settings', 'offer_selection'));	
	}
	public function zmcs_offer_selection($token, $data_only = false){
		$data = Q::offer_get($token, $langs_id='pl', $pageName = 'page', $pageNumber = null);
		$offer_selection = Q::offer_get_sellection($token);
		if($data_only == true) return $offer_selection;
		return view('themes.'.Config('zmcms.frontend.theme_name').'.backend.zmcms_website_offer_frm_selection', compact('offer_selection'));

	}
	public function zmcms_website_offer_update($data){
		$data['data']['images_resized'] = NULL;
		if(strlen($data['data']['ilustration']) > 4) $d['ilustration'] = zmcms_image_save(
				base_path().DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.$data['data']['ilustration'],
				base_path().DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.Config('zmcms.frontend.theme_name').DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'store'.DIRECTORY_SEPARATOR.'wo'.DIRECTORY_SEPARATOR.'ilustrations',
					str_slug(date('Y-m-d').'-'.$data['names']['name']).'.jpg'
		);
		$data['data']['images_resized'] = json_encode($d);
		return Q::offer_update($data);
		$result = [
			'result' =>'ok',
			'code' =>'ok',
			'msg' => '<pre>UPDATE: '.print_r($data, true).'</pre>',
		];
		return json_encode($result);
	}
	public function zmcms_website_offer_delete($token){
		$data = Q::offer_get($token, $langs_id='pl', $pageName = 'page', $pageNumber = null);
		$resultset = json_decode(Q::offer_delete($token));
		if(strlen($data['data']->images_resized)>0){

			foreach(json_decode(
				$data['data']->images_resized
			)->ilustration as $f)
				if(is_file(base_path().DIRECTORY_SEPARATOR.'public'.$f))unlink(base_path().DIRECTORY_SEPARATOR.'public'.$f);
		}
		return json_encode($resultset); 
	}

	public function object_add_frm(Request $request, $token, $type=null){
		$theme_name = Config('zmcms.frontend.theme_name');
		$btns=Config($theme_name.'.website_offer.objects');
		// return $type;
		if($type==null)$type= array_key_first($btns);
		// return $type;
		$data = \App::call(
			$btns[$type]['run'],
			[
				'request' => $request,
				'offer_token' => $token,
				'type'=>$type,
			]
		);
		return view('themes.'.$theme_name.'.backend.zmcms_website_offer_object_selec_frm', compact('btns', 'data'));
		$result = [
			'result' =>'ok',
			'code' =>'ok',
			'msg' => '<pre>object_add_frm: '.print_r($btns, true).'</pre>',
		];
		return json_encode($result);
	}

	public function get_object_list($object_type){
		return 'xxxxxxx'.$object_type.'xxxxxxx';
	}
	public function toggle_connection(Request $request){
		Q::offers_relations_toggle($request->all());
		return $this->object_add_frm($request, $request->all()['offer_token'], $type=$request->all()['object_type']);
		return '<pre>'.print_r($request->all(), true).'</pre>';
		return '11111111111111';
	}
	

}
