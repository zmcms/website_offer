<?php
namespace Zmcms\WebsiteOffer\Backend\Db;
use Illuminate\Support\Facades\DB;
use Session;
use Request;
class Queries{
	/**
	 * LISTA OFERT W SERWISIE
	 * Formaty zmiannych:
	 * 	$paginate (stronicowanie): 
	 *		if==0, wyświetla wszystko, 
	 * 		if==X: wyświetla wynik podzielony na strony, X elementów każda
	 * 	$order (sortowanie), tablica o poniższym formacie: 
	 * 		['sort' => 'asc', 'name' => 'desc']
	 * 	$filter (filtrowanie wyników)
	 * 		[
	 *			['langs_id', 	'=',		'pl'	],
	 *			['name', 		'rlike',	'główne'],
	 *		]
	 */
	public static function offers_list($paginate = 0, $order=[], $filter=[]){
		
		if(Session::has('db_filters'))
			$filter = Session::get('db_filters');
		// echo '<pre>'.print_r($filter, true).'</pre>';
		$offer = (Config('database.prefix')??'').'offers';
		$offer_names = (Config('database.prefix')??'').'offers_names';
		$resultset = DB::table($offer)
			->join($offer_names, $offer.'.token', '=', $offer_names.'.token');
		if($filter!=[])
			if(isset($filter['zwpol'])){
				$resultset->where('date_from', 'like', '%'.$filter['zwpol'].'%');
				$resultset->orWhere('date_to', 'like', '%'.$filter['zwpol'].'%');
				$resultset->orWhere('name', 'like', '%'.$filter['zwpol'].'%');
				$resultset->orWhere('slug', 'like', '%'.$filter['zwpol'].'%');
				$resultset->orWhere('intro', 'like', '%'.$filter['zwpol'].'%');
			}
			$resultset->select([
				$offer.'.token as token',
				$offer.'.sort as sort',
				$offer.'.access as access',
				$offer.'.frontend_access as frontend_access',
				$offer.'.active as active',
				$offer.'.ilustration as ilustration',
				$offer.'.images_resized as images_resized',
				$offer.'.date_from as date_from',
				$offer.'.date_to as date_to',
				$offer.'.created_at as created_at',
				$offer.'.updated_at as updated_at',
				$offer.'.type as type ',
				$offer.'.price_brut as price_brut ',
				$offer.'.price_brut_min as price_brut_min ',
				$offer.'.promo as promo ',
				$offer.'.points as points ',
				$offer_names.'.token as token',
				$offer_names.'.langs_id as langs_id_names',
				$offer_names.'.name as name',
				$offer_names.'.slug as slug',
				$offer_names.'.intro as intro',
				$offer_names.'.created_at as names_created_at',
				$offer_names.'.updated_at as names_updated_at',
			]);
		if($order!=[])
			foreach ($order as $column => $direction) {
				$resultset->orderBy($column, $direction);
			}
		if($paginate==0)
			return $resultset->get();

		return $resultset->paginate($paginate);
	}
	/**
	 * WYBIERANIE POJEDYNCZEGO ARTYKUŁU WG KRYTERIUM Z FILTRA
	 * Formaty zmiannych:
	 * 	$filter (filtrowanie wyników)
	 * 		[
	 *			['langs_id', 	'=',		'pl'	],
	 *			['name', 		'rlike',	'główne'],
	 *		]
	 * 	$order (sortowanie), tablica o poniższym formacie: 
	 * 		['sort' => 'asc', 'name' => 'desc']
	 */
	public static function offer_get($token, $langs_id, $pageName = 'page', $pageNumber = null){
		$offer = (Config('database.prefix')??'').'offers';
		$offer_names = (Config('database.prefix')??'').'offers_names';
		$resultset['data'] = DB::table($offer)
			->join($offer_names, $offer.'.token', '=', $offer_names.'.token')
			->where($offer.'.token', $token)
			->where($offer_names.'.langs_id', $langs_id)
			->select([
				$offer.'.token as token',
				$offer.'.sort as sort',
				$offer.'.access as access',
				$offer.'.frontend_access as frontend_access',
				$offer.'.active as active',
				$offer.'.ilustration as ilustration',
				$offer.'.images_resized as images_resized',
				$offer.'.date_from as date_from',
				$offer.'.date_to as date_to',
				$offer.'.type as type',
				$offer.'.price_brut as price_brut',
				$offer.'.price_brut_min as price_brut_min',
				$offer.'.promo as promo',
				$offer.'.points as points',
				$offer.'.created_at as created_at',
				$offer.'.updated_at as updated_at',
				$offer_names.'.token as token',
				$offer_names.'.langs_id as langs_id',
				$offer_names.'.name as name',
				$offer_names.'.slug as slug',
				$offer_names.'.intro as intro',
				$offer_names.'.created_at as names_created_at',
				$offer_names.'.updated_at as names_updated_at',
			])
			->first();
			return $resultset;
	}
	public static function offer_update($data){
		$offer = (Config('database.prefix')??'').'offers';
		$offer_names = (Config('database.prefix')??'').'offers_names';
		try{
			DB::beginTransaction();
			DB::table($offer)
				->where('token', $data['data']['token'])
				->update([
					'token'=>$data['data']['token'],
					'sort'=>$data['data']['sort'],
					'access'=>$data['data']['access'],
					'frontend_access'=>$data['data']['frontend_access'],
					'active'=>$data['data']['active'],
					'ilustration'=>$data['data']['ilustration'],
					'images_resized'=>$data['data']['images_resized'],
					'price_brut'=>$data['data']['price_brut'],
					'price_brut_min'=>$data['data']['price_brut_min'],
					'promo'=>$data['data']['promo'],
					'points'=>$data['data']['points'],
					'date_from'=>$data['data']['date_from'],
					'date_to'=>$data['data']['date_to'],
				]);
			DB::table($offer_names)
				->where('token', $data['data']['token'])
				->where('langs_id', $data['data']['langs_id'])
				->update([
					'token'=>$data['data']['token'],
					'langs_id'=>$data['data']['langs_id'],
					'name'=>$data['names']['name'],
					'slug'=>$data['names']['slug'],
					'intro'=>$data['names']['intro'],
				]);
			DB::commit();
			return json_encode([
				'result'	=>	'ok',
				'code'		=>	'ok',
				'msg' 		=>	___('Artykuł '.' "'.$data['names']['name'].'" został zaktualizowany.'),
			]);
		}catch(\Illuminate\Database\QueryException $e){
			DB::rollBack();
			return json_encode([
				'result'	=>	'error',
				'code'		=>	$e->getCode(),
				'msg' 		=>	___('Nie można dodać zaktualizować artykułu'.' "'.$data['names']['title'].'"'),
			]);
		}
	}
	public static function offer_delete($token){
		$offer = (Config('database.prefix')??'').'offers';
		try{
			DB::beginTransaction();
			DB::table($offer)
				->where('token', $token)
				->delete();
			DB::commit();
			return json_encode([
				'result'	=>	'ok',
				'code'		=>	'ok',
				'msg' 		=>	___('Oferta została usunięta.'),
			]);
		}catch(\Illuminate\Database\QueryException $e){
			DB::rollBack();
			return json_encode([
				'result'	=>	'error',
				'code'		=>	$e->getCode(),
				'msg' 		=>	___('Nie można usunąć oferty'),
			]);
		}
	}
	public static function offer_create($data){
		$offer = (Config('database.prefix')??'').'offers';
		$offer_names = (Config('database.prefix')??'').'offers_names';
		$token = hash ('sha256', date('Ymd').rand(0,1000));
		DB::table($offer)
			->insert([
				'token'=>$token,
				'sort'=>$data['data']['sort'],
				'access'=>$data['data']['access'],
				'frontend_access'=>$data['data']['frontend_access'],
				'active'=>$data['data']['active'],
				'type'=>$data['data']['type'],
				'ilustration'=>$data['data']['ilustration'],
				'images_resized'=>$data['data']['images_resized'],
				'price_brut'=>$data['data']['price_brut'],
				'price_brut_min'=>$data['data']['price_brut_min'],
				'promo'=>$data['data']['promo'],
				'points'=>$data['data']['points'],
				'date_from'=>$data['data']['date_from'],
				'date_to'=>$data['data']['date_to'],
			]);
		DB::table($offer_names)
			->insert([
				'token'=>$token,
				'langs_id'=>$data['data']['langs_id'],
				'name'=>$data['names']['name'],
				'slug'=>$data['names']['slug'],
				'intro'=>$data['names']['intro'],
			]);
		return json_encode([
				'result'	=>	'ok',
				'code'		=>	'ok',
				'msg' 		=>	___('Dodano nową ofertę.'),
			]);
		try{
			DB::beginTransaction();
			DB::commit();
			
		}catch(\Illuminate\Database\QueryException $e){
			DB::rollBack();
			return json_encode([
				'result'	=>	'error',
				'code'		=>	$e->getCode(),
				'msg' 		=>	___('Nie można dodać nowego artykułu.'),
			]);
		}
	}



/**
 * PRODUKTY
 */
	/**
	 * LISTA PRODUKTÓW W SERWISIE
	 * Formaty zmiannych:
	 * 	$paginate (stronicowanie): 
	 *		if==0, wyświetla wszystko, 
	 * 		if==X: wyświetla wynik podzielony na strony, X elementów każda
	 * 	$order (sortowanie), tablica o poniższym formacie: 
	 * 		['sort' => 'asc', 'name' => 'desc']
	 * 	$filter (filtrowanie wyników)
	 * 		[
	 *			['langs_id', 	'=',		'pl'	],
	 *			['name', 		'rlike',	'główne'],
	 *		]
	 */
	public static function products_list($paginate = 0, $order=[], $filter=[]){
		if(Session::has('db_filters'))
			$filter = Session::get('db_filters');
		$product = (Config('database.prefix')??'').'product';
		$product_names = (Config('database.prefix')??'').'product_names';
		$product_compositions = (Config('database.prefix')??'').'product_compositions';
		$product_description_content = (Config('database.prefix')??'').'product_description_content';
		$offers_relations = (Config('database.prefix')??'').'offers_relations';
		$offers = DB::table($offers_relations)
			->select([
				'offers_token',
				'object_token',
				'q',
			])
			->distinct();
		$compositions = DB::table($product_compositions)
			->select([
				'token'
			])->distinct();
		$resultset = DB::table($product)
			->join($product_names, $product.'.token', '=', $product_names.'.token')
			->leftJoinSub($compositions, $as='c', $product.'.token', $operator = '=', $second = 'c.token')
			->leftJoinSub($offers, $as='d', $product.'.token', $operator = '=', $second = 'd.object_token');
		if($filter!=[])
			if(isset($filter['zwppl'])){
				$resultset->orWhere('name', 'like', '%'.$filter['zwppl'].'%');
				$resultset->orWhere('slug', 'like', '%'.$filter['zwppl'].'%');
				$resultset->orWhere('meta_keywords', 'like', '%'.$filter['zwppl'].'%');
				$resultset->orWhere('meta_description', 'like', '%'.$filter['zwppl'].'%');
				$resultset->orWhere('og_description', 'like', '%'.$filter['zwppl'].'%');
				$resultset->orWhere('intro', 'like', '%'.$filter['zwppl'].'%');
			}
			$resultset->select([
				'c.token as completation',
				'd.offers_token as offers_token',
				$product.'.token as token',
				$product.'.code as code',
				$product.'.ean13 as ean13',
				$product.'.ean128 as ean128',
				$product.'.link as link',
				$product.'.ilustration as ilustration',
				$product.'.images_resized as images_resized',
				$product.'.on_sale as on_sale',
				$product.'.supply_type as supply_type',
				$product.'.created_at as created_at',
				$product.'.updated_at as updated_at',
				$product_names.'.token as token',
				$product_names.'.langs_id as langs_id_names',
				$product_names.'.name as name',
				$product_names.'.slug as slug',
				$product_names.'.in_composition_name as in_composition_name',
				$product_names.'.intro as intro',
				$product_names.'.meta_keywords as meta_keywords',
				$product_names.'.meta_description as meta_description',
				$product_names.'.og_title as og_title',
				$product_names.'.og_type as og_type',
				$product_names.'.og_url as og_url',
				$product_names.'.og_image as og_image',
				$product_names.'.og_description as og_description',
				$product_names.'.created_at as names_created_at',
				$product_names.'.updated_at as names_updated_at',
			]);
		if($order!=[])
			foreach ($order as $column => $direction) {
				$resultset->orderBy($column, $direction);
			}
		if($paginate==0)
			return $resultset->get();

		return $resultset->paginate($paginate);
	}
	public static function offers_relations_toggle($data){
		// dd($data);
		$offers_relations = (Config('database.prefix')??'').'offers_relations';
		$q = DB::table($offers_relations)
			->where('offers_token', $data['offer_token'])
			->where('object_token', $data['object_token']);
		$count=$q->count();
		if($count>0){
			$q->delete();
		}else{
			 DB::table($offers_relations)
			 	->insert([
			 		'offers_token' => $data['offer_token'],
					'object_token' => $data['object_token'],
					'q'=>1,
					'parameters'	=>json_encode(
						[
							'object_type'=>$data['object_type'],
							'object_name'=>$data['object_name'],
							'object_slug'=>$data['object_slug'],

						]
					)
			 	]);
		}
		return 'ok';
	}
	public static function offer_get_sellection($token){
		$offers_relations = (Config('database.prefix')??'').'offers_relations';
		return DB::table($offers_relations)
			->where('offers_token', $token)
			->get();
	}


	public static function product_get($token){
		$product = (Config('database.prefix')??'').'product';
		$product_names = (Config('database.prefix')??'').'product_names';
		$product_compositions = (Config('database.prefix')??'').'product_compositions';
		$product_description_content = (Config('database.prefix')??'').'product_description_content';
		$dict_groups = (Config('database.prefix')??'').'product_compositions_groups';
		$offers_relations = (Config('database.prefix')??'').'offers_relations';
		$offer_names = (Config('database.prefix')??'').'offers_names';
		$offers = DB::table($offers_relations)
			->select([
				'offers_token',
				'object_token',
				'q',
			])
			->distinct();
		$compositions = DB::table($product_compositions)
			->select([
				'token'
			])->distinct();
		$resultset['product'] = DB::table($product)
			->leftJoin($product_names, $product.'.token', '=', $product_names.'.token')
			->leftJoin($product_description_content, $product.'.token', '=', $product_description_content.'.token')
			->where($product.'.token', $token)
			->select([
				$product.'.token as token',
				$product.'.code as code',
				$product.'.ean13 as ean13',
				$product.'.ean128 as ean128',
				$product.'.link as link',
				$product.'.ilustration as ilustration',
				$product.'.images_resized as images_resized',
				$product.'.on_sale as on_sale',
				$product.'.supply_type as supply_type',
				$product.'.created_at as created_at',
				$product.'.updated_at as updated_at',
				$product_names.'.token as token',
				$product_names.'.langs_id as langs_id_names',
				$product_names.'.name as name',
				$product_names.'.slug as slug',
				$product_names.'.in_composition_name as in_composition_name',
				$product_names.'.intro as intro',
				$product_names.'.meta_keywords as meta_keywords',
				$product_names.'.meta_description as meta_description',
				$product_names.'.og_title as og_title',
				$product_names.'.og_type as og_type',
				$product_names.'.og_url as og_url',
				$product_names.'.og_image as og_image',
				$product_names.'.og_description as og_description',
				$product_names.'.created_at as names_created_at',
				$product_names.'.updated_at as names_updated_at',
				$product_description_content.'.description',
			])->first();
		/**
		 * ZŁOŻENIE WERSJI PRODUKTU
		 */
		
		// $resultset['compositions']=self::product_composition_tree(self::product_composition_get($token), $token);
		// $resultset['versions']=self::get_product_versions($token, 20);
		// $resultset['versions'] =self::get_product_compositions_by_rid(
		 		// self::get_product_versions_compositions($token)
		 	// );
		$resultset['versions'] = self::product_composition_tree_2(
			$token,
			self::get_product_compositions_by_rid(
		 		self::get_product_versions_compositions($token)
		 	)
		);
		// zmcms_website_product_versions_simple
		
		/**
		 * SŁOWNIKI
		 */
		$dict_groups = (Config('database.prefix')??'').'product_compositions_groups';
		$dict_groups_names = (Config('database.prefix')??'').'product_compositions_groups_names';
		$resultset['dict']['groups'] = DB::table($dict_groups)
			->join($dict_groups_names, $dict_groups.'.group', '=', $dict_groups_names.'.group')
			->where('langs_id', 'pl')
			->select([
				$dict_groups.'.group as group',
				$dict_groups_names.'.name as name',
				$dict_groups.'.choices as choices',
			])
			->orderBy('name', 'asc')->get();


		$resultset['offers_relations']=DB::table($offers_relations)
			->join($offer_names, $offers_relations.'.offers_token', '=', $offer_names.'.token')
			->where('object_token', $token)
			->select([
				'offers_token',
				'object_token',
				'q',
				'name',
				'slug',
			])
			->get();
		return $resultset;
	}

	public static function product_composition_get($token, $array = true){
		// echo '<pre>'.print_r(Session::get('sorting'), true).'</pre>';
		$filter = [];
		if(Session::has('sorting'))
			$filter = Session::get('sorting');
		$product_names = (Config('database.prefix')??'').'product_names';
		$product_compositions = (Config('database.prefix')??'').'product_compositions';
		$product_compositions_names = (Config('database.prefix')??'').'product_compositions_names';
		$dict_groups = (Config('database.prefix')??'').'product_compositions_groups';
		$dict_groups_names = (Config('database.prefix')??'').'product_compositions_groups_names';

		$t0=DB::table($dict_groups)
			->join($dict_groups_names, $dict_groups.'.group', '=', $dict_groups_names.'.group')
			->where('langs_id', 'pl')
			->select([
				$dict_groups.'.group as group',
				$dict_groups_names.'.name as name',
				$dict_groups.'.choices as choices',
				$dict_groups.'.obligatory  as obligatory',

			]);
		$t1=DB::table($product_compositions)
			->join($product_names, $product_compositions.'.token', '=', $product_names.'.token')
			->join($product_compositions_names, $product_compositions.'.rid', '=', $$product_compositions_names.'.rid')
			->leftJoinSub($t0, $as='c', $product_compositions.'.group', $operator = '=', $second = 'c.group')
			->where($product_compositions.'.token', $token)
			->where($product_compositions_names.'.langs_id', (Session::get('language')??'pl'))
			->select([
				$product_compositions.'.composition_token as t',
				$product_compositions.'.token as p',
				$product_compositions.'.rid as rid',
				$product_compositions_names.'.name as composition_name',
				$product_names.'.name as name',
				$product_compositions.'.sort as sort',
				$product_compositions.'.group as group',
				'c.name as group_name',
				'c.choices as choices',
				'c.obligatory as obligatory',
				// $product_compositions.'.select as select',
				$product_compositions.'.price_affected as price_affected',
				$product_compositions.'.default as default',
				$product_compositions.'.default_q as default_q',
				$product_compositions.'.max_q as max_q',
				$product_compositions.'.price_brut as price_brut',
				$product_compositions.'.vat as vat',
			]);
		$t2=DB::table($product_compositions)
			->join($product_names, $product_compositions.'.composition_token', '=', $product_names.'.token')
			->join($product_compositions_names, $product_compositions.'.rid', '=', $$product_compositions_names.'.rid')
			->leftJoinSub($t0, $as='c', $product_compositions.'.group', $operator = '=', $second = 'c.group')
			->whereIn($product_compositions.'.token', function ($query) use($product_compositions, $token) {
				return $query->select(['composition_token'])
				->from($product_compositions)
				->where('token', $token);
			})
			->where($product_compositions_names.'.langs_id', (Session::get('language')??'pl'))
			->where($product_compositions.'.token', $token)
			->select([
				$product_compositions.'.composition_token as t',
				$product_compositions.'.token as p',
				$product_compositions.'.rid as rid',
				$product_compositions_names.'.name as composition_name',
				$product_names.'.name as name',
				$product_compositions.'.sort as sort',
				$product_compositions.'.group as group',
				'c.name as group_name',
				'c.choices as choices',
				'c.obligatory as obligatory',
				// $product_compositions.'.select as select',
				$product_compositions.'.price_affected as price_affected',
				$product_compositions.'.default as default',
				$product_compositions.'.default_q as default_q',
				$product_compositions.'.max_q as max_q',
				$product_compositions.'.price_brut as price_brut',
				$product_compositions.'.vat as vat',

			])
			->union($t1)
			->when(isset($filter['zwpcfvl']), function ($query) use ($filter){
				foreach($filter['zwpcfvl'] as $k => $v){
					$query->orderBy($k, $v);
				}
				return $query;
			})
			->get();
// if(isset($filter['zwpcfvl'])){
				// foreach($filter['zwpcfvl'] as $k => $v)
					// echo print_r($v, true);
					// $t2->orderBy($k, $v);
			// }
		if($array != false){
			$a=$b=[];
			foreach ($t2 as $v){
				foreach ($v as $key => $value) {
					$b[$key]=$value;
				}
				$a[] = $b;
			}
			// return array_merge($a, ['tree'=>self::product_composition_tree($a, $token)]);
			return $a;
		}

		
		return $t2;
	}
//  $perPage = 15, $columns = ['*'], $pageName = 'page', $page = null
	public static function product_completation_linker_list($token, $paginate = 20, $order=[], $filter=[], $pageName='page', $format = null){

		$product_names = (Config('database.prefix')??'').'product_names';
		$product_compositions = (Config('database.prefix')??'').'product_compositions';
		$t1=DB::table($product_compositions)
			->where('token', $token);
		$t2=DB::table($product_names)
		->leftJoinSub($t1, $as='c', $product_names.'.token', $operator = '=', $second = 'c.composition_token')
		->where($product_names.'.token', '<>', $token)

			->select([
				// $product_compositions.'.composition_token as t',
				// $product_compositions.'.token as p',
				// $product_compositions.'.rid as rid',
				$product_names.'.token as token',
				'c.rid as rid',
				'c.composition_token as composition_token',
				'c.sort as sort',
				'c.group as group',
				'c.select as select',
				'c.price_affected as price_affected',
				'c.default as default',
				'c.default_q as default_q',
				'c.max_q as max_q',
				$product_names.'.name as name',
				$product_names.'.slug as slug',
			]);
			if($paginate!=0)
				return $t2->paginate($paginate);
			if($format == null)
				return $t2->get();
			switch($format){
				case 'array':{return $t2->toArray(); break;}
				case 'json':{return $t2->toJson(); break;}
			}
	}
	public static function product_composition_tree($data, $token){
		$arr = $data;
		$a = [];
		foreach ($arr as $v) {
			
			if($v['p']==$token){
				$child_exists = array_search($v['t'], array_column($arr, 'p'));
				if(strlen($child_exists)==0)
					$a[]=array_merge($v);
				else
					$a[]=array_merge($v, ['children'=>self::product_composition_tree($arr, $v['t'])]);
			}
		}
		return $a;
	}

	public static function product_composition_tree_2($token, $data, $rid=null){
		// return $data;
		$arr = [];
		if($rid==null){
			foreach($data as $d){
				if($d['token'] == $token){
					$child_exists = array_search($d['rid'], array_column($data, 'p'));
					if(strlen($child_exists)==0)
						$arr[]=array_merge($d);
					else
						$arr[]=array_merge($d, ['children'=>self::product_composition_tree_2($token, $data, $d['rid']),]);
				}
			}
		}else{
			foreach($data as $d){
				if($d['p'] == $rid){
					$child_exists = array_search($d['rid'], array_column($data, 'p'));
					if(strlen($child_exists)==0)
						$arr[]=array_merge($d);
					else
						$arr[]=array_merge($d, ['children'=>self::product_composition_tree_2($token, $data, $d['rid']),]);
				}
			}
		}
		

		return $arr;
	}
	public static function product_update($data){
		$product = (Config('database.prefix')??'').'product';
		$product_names = (Config('database.prefix')??'').'product_names';
		$product_description_content = (Config('database.prefix')??'').'product_description_content';

		try{
			DB::beginTransaction();
			DB::table($product)
				->where('token', $data['data']['token'])
				->update([
					'link'=>$data['data']['link'],
					'code'=>$data['data']['code'],
					'ean13'=>$data['data']['ean13'],
					'ean128'=>$data['data']['ean128'],
					'on_sale'=>$data['data']['on_sale'],
					'supply_type'=>$data['data']['supply_type'],
					'token'=>$data['data']['token'],
					'ilustration'=>$data['data']['ilustration'],
					'images_resized'=>$data['data']['images_resized'],
				]);
			DB::table($product_names)
				->where('token', $data['data']['token'])
				->where('langs_id', $data['names']['langs_id'])
					->update([
					'name'=>$data['names']['name'],
					'in_composition_name'=>$data['names']['in_composition_name'],
					'slug'=>$data['names']['slug'],
					'langs_id'=>$data['names']['langs_id'],
					'intro'=>$data['names']['intro'],
					'meta_keywords'=>$data['names']['meta_keywords'],
					'meta_description'=>$data['names']['meta_description'],
					'og_type'=>$data['names']['og_type'],
					'og_url'=>$data['names']['og_url'],
					'og_image'=>$data['names']['og_image'],
					'og_description'=>$data['names']['og_description'],
					]);
			$count = DB::table($product_description_content)
				->where('token', $data['data']['token'])
				->where('langs_id', $data['names']['langs_id'])
				->count();
			if($count>0){
				DB::table($product_description_content)
				->where('token', $data['data']['token'])
				->where('langs_id', $data['names']['langs_id'])
				->update([
					'description'=>$data['content']['description'],
				]);			
			}else{
				DB::table($product_description_content)
				->insert([
					'token'=>$data['data']['token'],
					'langs_id'=>$data['names']['langs_id'],
					'description'=>$data['content']['description'],
				]);			
			}
			
			DB::commit();
			return json_encode([
				'result'	=>	'ok',
				'code'		=>	'ok',
				'msg' 		=>	___('Zaktualizowano dane produktu'),
			]);
		}catch(\Illuminate\Database\QueryException $e){
			DB::rollBack();
			return json_encode([
				'result'	=>	'err',
				'code'		=>	'dbwoq538',
				'msg' 		=>	___('Produktu nie można zaktualizować'),
			]);
		}
	}
	public static function product_create($data){
		$product = (Config('database.prefix')??'').'product';
		$product_names = (Config('database.prefix')??'').'product_names';
		$product_description_content = (Config('database.prefix')??'').'product_description_content';
		$token = 'wp_'.hash ('sha256', date('Ymd').rand(0,1000));
		
		try{
			DB::beginTransaction();
			DB::table($product)
				->insert([
					'token'=>$token,
					'link'=>$data['data']['link'],
					'code'=>$data['data']['code'],
					'ean13'=>$data['data']['ean13'],
					'ean128'=>$data['data']['ean128'],
					'on_sale'=>$data['data']['on_sale'],
					'supply_type'=>$data['data']['supply_type'],
					'ilustration'=>$data['data']['ilustration'],
					'images_resized'=>$data['data']['images_resized'],
				]);
			DB::table($product_names)
					->insert([
					'token'=>$token,
					'name'=>$data['names']['name'],
					'in_composition_name'=>$data['names']['in_composition_name'],
					'slug'=>$data['names']['slug'],
					'langs_id'=>$data['names']['langs_id'],
					'intro'=>$data['names']['intro'],
					'meta_keywords'=>$data['names']['meta_keywords'],
					'meta_description'=>$data['names']['meta_description'],
					'og_type'=>$data['names']['og_type'],
					'og_url'=>$data['names']['og_url'],
					'og_image'=>$data['names']['og_image'],
					'og_description'=>$data['names']['og_description'],
					]);
				DB::table($product_description_content)
				->insert([
					'token'=>$token,
					'langs_id'=>$data['names']['langs_id'],
					'description'=>$data['content']['description'],
				]);			
			
			DB::commit();
			return json_encode([
				'result'	=>	'ok',
				'code'		=>	'ok',
				'msg' 		=>	___('Produkt dodany'),
			]);
		}catch(\Illuminate\Database\QueryException $e){
			DB::rollBack();
			return json_encode([
				'result'	=>	'err',
				'code'		=>	'dbwoq538',
				'msg' 		=>	___('Nie dodano produktu'),
			]);
		}
	}
	public static function delete_composition_part($rid){
		$product_compositions = (Config('database.prefix')??'').'product_compositions';
		$count = DB::table($product_compositions)
			->where('p', $rid)
			->count();
		if($count>0)
			return json_encode([
				'result'	=>	'error',
				'code'		=>	'dbwoq768',
				'msg' 		=>	___($count.'Ta pozycja ma podpozycje'),
			]);
		DB::table($product_compositions)
			->where('rid', $rid)
			->delete();
		return json_encode([
				'result'	=>	'ok',
				'code'		=>	'ok',
				'msg' 		=>	___('Usunięto'),
			]);
	}
	public static function update_composition_part($data){
		$product_compositions = (Config('database.prefix')??'').'product_compositions';
		$product_compositions_names = (Config('database.prefix')??'').'product_compositions_names';
		DB::table($product_compositions)
			->where('rid', $data['rid'])
			->update([
    			'composition_token'			=>	$data['token'],
    			// 'token'		=>	$data['parent'],
    			'sort'			=>	$data['sort'],
    			// 'name'			=>	$data['name'],
    			'group'			=>	$data['group'],
    			'select'		=>	$data['select'] ?? null,
    			'price_affected'=>	$data['price_affected'],
    			'default'		=>	$data['default'],
    			'default_q'		=>	$data['default_q'],
    			'max_q'			=>	$data['max_q'],
			]);
		DB::table($product_compositions_names)
			->where('rid', $data['rid'])
			->where('langs_id', Session::get('language'))
			->update([
				'name' => $data['name'],
			]);
	}
	public static function create_composition_part($data){
		// echo json_encode([
				// 'result'	=>	'err',
				// 'code'		=>	'dbwoq538',
				// 'msg' 		=>	'<pre>'.___(print_r($data, true)).'</pre>',
			// ]);
		$product_compositions = (Config('database.prefix')??'').'product_compositions';
		$rid = 'wpc_'.hash ('sha256', date('Ymd').rand(0,1000));
		if($data['product_token']!=$data['composition_token'])
		DB::table($product_compositions)
			->insert([
				'rid'				=>($rid),
				'p'					=>($data['p']??null),
				'token'				=>($data['product_token'] ?? null),
				'composition_token'	=>($data['composition_token'] ?? null),
				'sort'				=>($data['sort'] ?? null),
				'group'				=>($data['group'] ?? null),
				'select'			=>($data['select'] ?? null),
				'price_affected'	=>($data['price_affected'] ?? 0),
				'default'			=>($data['default'] ?? 0),
				'default_q'			=>($data['default_q'] ?? 1),
				'max_q'				=>($data['max_q'] ?? 1),
			]);
	}
	public static function completations_dict($paginate = 0, $order=[], $filter=[], $format=null){
		// Session::forget('sorting');
		if((Session::has('sorting')))
			$order=Session::get('sorting');
		if((Session::has('db_filters')))
			$filter = Session::get('db_filters');
		// echo '<pre>'.print_r($filter, true).'</pre>';
		$product_compositions_groups=(Config('database.prefix')??'').'product_compositions_groups';
		$product_compositions_groups_names=(Config('database.prefix')??'').'product_compositions_groups_names';
		$q = DB::table($product_compositions_groups)
		->join($product_compositions_groups_names, $product_compositions_groups.'.group', '=', $product_compositions_groups_names.'.group');
		if(isset($order['zwpcr']) && $order['zwpcr']!=[]){
			foreach ($order['zwpcr'] as $k => $v) {
				$q->orderBy($k, $v);
			}
		}
		if($filter!=[]){
			if(isset($filter['zwpcr'])){
				if(strlen($filter['zwpcr'])>0){
					$q->where('name', 'like', '%'.$filter['zwpcr'].'%');
					$q->orWhere($product_compositions_groups.'.group', 'like', '%'.$filter['zwpcr'].'%');
					$q->orWhere('choices', 'like', '%'.$filter['zwpcr'] .'%');
					$q->orWhere('run', 'like', '%'.$filter['zwpcr'].'%');
				}
			}
		}
		// echo $filter['zwpcr'];

		$q->select([
			$product_compositions_groups.'.group as group',
			$product_compositions_groups.'.sort as sort',
			$product_compositions_groups.'.choices as choices',
			$product_compositions_groups.'.run as run',
			$product_compositions_groups_names.'.langs_id as langs_id',
			$product_compositions_groups_names.'.name as name',
		]);

		// return $q;
		if($paginate==0){
			if($format == null)
				return $q->get();
			switch($format){
				case 'array':{return $q->get()->toArray(); break;}
				case 'json':{return $q->get()->toJson(); break;}
			}
		}else{
			return $q->paginate($paginate);
		}
		
	}
	public static function create_product_version($data){
		$product_compositions = (Config('database.prefix')??'').'product_compositions';
		$product_compositions_names = (Config('database.prefix')??'').'product_compositions_names';
		$rid = 'wpv_'.hash ('sha256', date('Ymd').rand(0,10000));
		try{
			DB::beginTransaction();
			DB::table($product_compositions)
			->insert([
				'rid'				=>($rid),
				'p'					=>($data['p']??null),
				'token'				=>($data['token'] ?? null),
				'composition_token'	=>($data['composition_token'] ?? null),
				'sort'				=>($data['sort'] ?? null),
				'group'				=>($data['group'] ?? null),
				'select'			=>($data['select'] ?? null),
				'price_affected'	=>($data['price_affected'] ?? 0),
				'default'			=>($data['default'] ?? 0),
				'default_q'			=>($data['default_q'] ?? 1),
				'max_q'				=>($data['max_q'] ?? 1),
				'price_brut'		=>($data['price_brut'] ?? null),
				'vat'				=>($data['vat'] / 100 ?? null),
			]);
			DB::table($product_compositions_names)
			->insert([
				'rid'				=>($rid),
				'langs_id'	=>('pl'),
				'name'		=>($data['name'])
			]);
			DB::commit();
			return json_encode([
				'result'	=>	'ok',
				'code'		=>	'ok',
				'msg' 		=>	___('Dodano nową wersję produktu'),
			]);
		}catch(\Illuminate\Database\QueryException $e){
			DB::rollBack();
			return json_encode([
				'result'	=>	'error',
				'code'		=>	'dbwoq844',
				'msg' 		=>	___('Nie dodano wersji. Sprawdź, czy dana wersja już nie istnieje lub czy zostały wypełnine wszystkie wymagane pola.'.$e->getMessage()),
				'dbmsg'=>$e->getMessage(),
			]);
		}
	}
	public static function get_product_versions_compositions($token = null, $rid = null){
		$product_compositions = (Config('database.prefix')??'').'product_compositions';
		$product_compositions_names = (Config('database.prefix')??'').'product_compositions_names';
		$x=[];
		
		if($rid==null){
			$t0 = DB::table($product_compositions)
			->where('token', $token)
			->select([
				'rid',
				'p',
			]);
			$t1 = DB::table($product_compositions)
			->whereIn
			($product_compositions.'.p', function ($query) use($product_compositions, $token) {
				return $query->select(['rid'])
				->from($product_compositions)
				->where('token', $token);
			})
			->select([
				'rid',
				'p',
			])->union($t0)
			->get();
		}else{
			$t1 = DB::table($product_compositions)
			->where($p, $rid)
			->select([
				'rid',
				'p',
			])
			->get();
		}
		$arr = $arr1 = [];
		foreach($t1 as $r)
			$arr[]=['rid'=>$r->rid, 'p'=>$r->p,];
		$tst =[];
		if($arr !=[])
			foreach($arr as $r)
				if(strlen($r['p'])>0)
					$tst[$r['rid']] = $r['rid'];
		$tst = array_values($tst);
		$cnt = DB::table($product_compositions)
			->whereIn('p', $tst)
			->select([
				'rid',
				'p',
			])
			->count();
		if($cnt>0)
			$arr1 = self::get_product_versions_childrens_by_rid($tst);

		$arr = array_merge($arr, $arr1);
		foreach($arr as $s)
			$x[]=$s['rid'];
		return $x;
		return ['arr'=>$arr, 'tst'=>$tst, 'cnt'=>$cnt];
	}
	public static function get_product_versions_childrens_by_rid($tst){
		$product_compositions = (Config('database.prefix')??'').'product_compositions';
		$product_compositions_names = (Config('database.prefix')??'').'product_compositions_names';
		$arr =[];
		$res = DB::table($product_compositions)
			->whereIn('p', $tst)
			->select([
				'rid',
				'p',
			])
		->get();
		foreach($res as $r)
			$arr[]=['rid'=>$r->rid, 'p'=>$r->p,];
		$tst =[];
		if($arr !=[])
			foreach($arr as $r)
				if(strlen($r['p'])>0)
					$tst[$r['rid']] = $r['rid'];
		$tst = array_values($tst);
		$cnt = DB::table($product_compositions)
			->whereIn('p', $tst)
			->select([
				'rid',
				'p',
			])
			->count();
		$arr1=[];
		if($cnt>0)
			$arr1 = self::get_product_versions_childrens_by_rid($tst);
		$arr=array_merge($arr, $arr1);
		return $arr;

	}

	public static function get_product_compositions_by_rid($arr){
		$product_compositions = (Config('database.prefix')??'').'product_compositions';
		$product_compositions_names = (Config('database.prefix')??'').'product_compositions_names';

		$product = (Config('database.prefix')??'').'product';
		$product_names = (Config('database.prefix')??'').'product_names';

		$product_compositions_groups  = (Config('database.prefix')??'').'product_compositions_groups';
		$product_compositions_groups_names  = (Config('database.prefix')??'').'product_compositions_groups_names';

		$res=[];
		$t_prod = DB::table($product)
			->join($product_names, $product.'.token', '=', $product_names.'.token')
			->where('langs_id', Session::get('language'))
			->select([
				$product.'.token',
				$product.'.code',
				$product.'.producer_code',
				$product.'.producer',
				$product.'.images_resized',
				$product_names.'.name as product_name',
				$product_names.'.slug as product_slug',
			]);
		$t_groups =  DB::table($product_compositions_groups)
			->join($product_compositions_groups_names, $product_compositions_groups.'.group', '=', $product_compositions_groups_names.'.group')
			->where('langs_id', Session::get('language'))
			->select([
				$product_compositions_groups.'.group',
				$product_compositions_groups.'.sort',
				$product_compositions_groups.'.choices',
				$product_compositions_groups.'.run',
				$product_compositions_groups.'.obligatory',
				$product_compositions_groups_names.'.name',
			]);

		$resultset = DB::table($product_compositions)
			->leftJoin($product_compositions_names, $product_compositions.'.rid', '=', $product_compositions_names.'.rid')
			->leftJoinSub($t_prod, $as='c', $product_compositions.'.token', $operator = '=', $second = 'c.token')
			->leftJoinSub($t_groups, $as='d', $product_compositions.'.group', $operator = '=', $second = 'd.group')
			->whereIn($product_compositions.'.rid', $arr)
			->select([
				$product_compositions.'.rid as rid',
				$product_compositions.'.p as p',
				$product_compositions.'.token as token',
				$product_compositions.'.composition_token as composition_token',
				$product_compositions.'.sort as sort',
				$product_compositions.'.group as group',
				$product_compositions.'.price_affected as price_affected',
				$product_compositions.'.default as default',
				$product_compositions.'.default_q as default_q',
				$product_compositions.'.max_q as max_q',
				$product_compositions.'.price_brut as price_brut',
				$product_compositions.'.vat as vat',
				$product_compositions_names.'.langs_id as langs_id',
				$product_compositions_names.'.name as composition_name',
				'c.product_name as product_name',
				'c.images_resized as images_resized',
				'd.obligatory',
				'd.choices',
				'd.sort as group_sort',
				'd.run',

			])->get();
		foreach($resultset as $r)
			$res[]=[
				'rid'=>$r->rid,
				'p'=>$r->p,
				'token'=>$r->token,
				'composition_token'=>$r->composition_token,
				'sort'=>$r->sort,
				'group'=>$r->group,
				'price_affected'=>$r->price_affected,
				'default'=>$r->default,
				'default_q'=>$r->default_q,
				'max_q'=>$r->max_q,
				'price_brut'=>$r->price_brut,
				'vat'=>$r->vat,
				'langs_id'=>$r->langs_id,
				'composition_name'=>$r->composition_name,
				'images_resized'=>$r->images_resized,
				'obligatory'=>$r->obligatory,
				'choices'=>$r->choices,
				'group_sort'=>$r->group_sort,
				'run'=>$r->run,
			];
			return $res;
	}


	public static function get_product_versions($token, $paginate = 0){
		$product_compositions = (Config('database.prefix')??'').'product_compositions';
		$product_compositions_names = (Config('database.prefix')??'').'product_compositions_names';
		$q = DB::table($product_compositions)
			->join($product_compositions_names, $product_compositions_names.'.rid', '=', $product_compositions.'.rid')
			->where($product_compositions.'.token', $token)
			->select([
				$product_compositions.'.rid as rid',
				$product_compositions.'.p as p',
				$product_compositions.'.token as token',
				$product_compositions.'.composition_token as composition_token',
				$product_compositions.'.sort as sort',
				$product_compositions.'.group as group',
				$product_compositions.'.select as select',
				$product_compositions.'.price_affected as price_affected',
				$product_compositions.'.default as default',
				$product_compositions.'.default_q as default_q',
				$product_compositions.'.max_q as max_q',
				$product_compositions.'.price_brut as price_brut',
				$product_compositions.'.vat as vat',
				$product_compositions_names.'.langs_id as langs_id',
				$product_compositions_names.'.name as name',

			]);
		if($paginate != 0)
			return $q->paginate($paginate);

		return $q->get();

	}

}
/**
 * 

$tblName=$tblNamePrefix.'product_compositions';
Schema::create($tblName, function($table){$table->string('rid', 70)->nullable()->unique()->before('token');});//Unikalny identyfikator rekordu tabeli
Schema::table($tblName, function($table){$table->string('p', 70)->nullable();});//Rodzic (wskazuje na rid)
Schema::table($tblName, function($table){$table->string('token', 70)->nullable();});//Token produktu będącego składnikiem
Schema::table($tblName, function($table){$table->string('composition_token', 70)->nullable()->after('token');});//Token produktu będącego składnikiem
Schema::table($tblName, function($table){$table->integer('sort', false, true)->nullable();});	//	Sortowanie kolejności wyświetlania rekordów
Schema::table($tblName, function($table){$table->string('group', 20)->nullable();});	//	Grupa opcji (Np. wybór ciasta do pizzy, kolor itd itp)
Schema::table($tblName, function($table){$table->string('select', 20)->nullable();});	//	Checkbox lub radio
Schema::table($tblName, function($table){$table->string('price_affected', 1);}); //Czy wpływa na cenę? 0 - niw wpływa na cenę, 1 - wpływa (dodawanie i odejmowanie), 2 - wpływa częściowo (gdy tylko dodajemy)
Schema::table($tblName, function($table){$table->string('default', 1);}); //Czy ta część jest domyślnie wybrana? 0 - NIE, 1 - TAK
Schema::table($tblName, function($table){$table->decimal('default_q', 12, 2);}); //Domyślna ilość składnika
Schema::table($tblName, function($table){$table->decimal('max_q', 12, 2);}); //MAksymalna ilość składnika
Schema::table($tblName, function($table){$table->decimal('price_brut', 12, 2)->nullable();});		//	Cena do zapłaty danego składnika
Schema::table($tblName, function($table){$table->decimal('vat', 2, 2)->nullable();}); //VAT w setnych całości, np. 0.23
Schema::table($tblName, function($table){$table->primary(['rid'], 'zmcmspckey1');});
Schema::table($tblName, function($table) use ($tblNamePrefix){$table->foreign('token')->references('token')->on($tblNamePrefix.'product')->onUpdate('cascade')->onDelete('cascade');});
Schema::table($tblName, function($table) use ($tblNamePrefix){$table->foreign('composition_token')->references('token')->on($tblNamePrefix.'product')->onUpdate('cascade')->onDelete('cascade');});

$tblName=$tblNamePrefix.'product_compositions_names';
Schema::create($tblName, function($table){$table->string('rid', 70);});//Unikalny identyfikator rekordu tabeli
Schema::table($tblName, function($table){$table->string('langs_id', 5);});
Schema::table($tblName, function($table){$table->string('name', 70);});
Schema::table($tblName, function($table){$table->primary(['rid', 'langs_id'], 'zmcmspckey1n');});
Schema::table($tblName, function($table) use ($tblNamePrefix){$table->foreign('rid')->references('rid')->on($tblNamePrefix.'product_compositions')->onUpdate('cascade')->onDelete('cascade');});


rid
p
token
composition_token
sort
group
select
price_affected
default
default_q
max_q
price_brut
vat
)
)
 */