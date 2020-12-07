<?php
namespace Zmcms\WebsiteOffer\Frontend\Db;
use Illuminate\Support\Facades\DB;
use Session;
use Request;
class Queries{
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
	public static function products_list($nav_token, $obj_token = null, $paginate = 0, $order=[], $filter=[]){
		if(Session::has('db_frontend_filters'))
			$filter = Session::get('db_frontend_filters');
		if(Session::has('db_frontend_sorting'))
			$order = Session::get('db_frontend_sorting');
		
		$product = (Config('database.prefix')??'').'product';
		$product_names = (Config('database.prefix')??'').'product_names';
		$product_compositions = (Config('database.prefix')??'').'product_compositions';
		$website_navigations_linker=(Config('database.prefix')??'').'website_navigations_linker';
		$compositions = DB::table($product_compositions)
			->select([
				'token'
			])->distinct();
		$resultset = DB::table($product)
			->join($website_navigations_linker, $website_navigations_linker.'.obj_token', '=', $product.'.token')
			->join($product_names, $product.'.token', '=', $product_names.'.token')
			->leftJoinSub($compositions, $as='c', $product.'.token', $operator = '=', $second = 'c.token')
			->where($website_navigations_linker.'.nav_token', $nav_token)
			->where($product.'.on_sale', '1')
			->when($obj_token != null, function ($query) use ($product, $obj_token){
				return $query->where($product.'.token', $obj_token);
			})
			->select([
				$product.'.token as token',
				'c.token as composition',
				$product.'.images_resized as images_resized',
				$product.'.on_sale as on_sale',
				$product_names.'.langs_id as langs_id_names',
				$product_names.'.name as name',
				$product_names.'.slug as slug',
				$product_names.'.intro as intro',
			]);
		if($order!=[])
			foreach ($order as $column => $direction) {
				$resultset->orderBy($column, $direction);
			}
		if($paginate==0)
			return $resultset->get();
		return $resultset->paginate($paginate);
	}


	public static function product_composition($token){
		return \Zmcms\WebsiteOffer\Backend\Db\Queries::product_composition_get($token, $array = true);
		// $product_compositions = (Config('database.prefix')??'').'product_compositions';
		// $product_compositions_names = (Config('database.prefix')??'').'product_compositions_names';
		// return $resultset = DB::table($product_compositions)
			// ->join($product_compositions_names, $product_compositions_names.'.rid', '=', $product_compositions.'.rid')
			// ->where($product_compositions.'.token', $token)
			// ->orderBy($product_compositions.'.sort', 'asc')
			// ->orderBy($product_compositions_names.'.name', 'asc')
			// ->select([
				// $product_compositions.'.rid as rid',
				// $product.'.token as token',
				// $product.'.code as code',
				// $product.'.on_sale as on_sale',
				// $product_names.'.name as product_name',
				// $product_compositions.'.group as group',
				// $product_compositions.'.sort as composition_sort',
				// $product_compositions_groups.'.choices as choices',
				// $product_compositions_groups.'.obligatory as obligatory',
				// $product_compositions_groups_names.'.name as group_name',
				// $product_compositions.'.price_affected as price_affected',
				// $product_compositions.'.default as default',
				// $product_compositions.'.default_q as default_q',
				// $product_compositions.'.max_q as max_q',
				// $product_compositions.'.price_brut as price_brut',
				// $product_compositions.'.vat as vat',
				// $product_compositions_names.'.name as composition_name',
			// ])
			// ->get();
		// $arr = [];
		// foreach($resultset as $r){
			// $arr[$r->group]['group']=$r->group;
			// $arr[$r->group]['group_name']=$r->group_name;
			// $arr[$r->group]['group_choices']=$r->choices;
			// $arr[$r->group]['data'][]=$r;
		// }
		// return $arr;
	}

	public static function get_product($token){
		$product = (Config('database.prefix')??'').'product';
		$product_names = (Config('database.prefix')??'').'product_names';
		$product_description_content = (Config('database.prefix')??'').'product_description_content';
		$product_compositions = (Config('database.prefix')??'').'product_compositions';
		$compositions = DB::table($product_compositions)
			->select([
				'token'
			])->distinct();
		$resultset['product'] = DB::table($product)
			->join($product_names, $product_names.'.token', '=', $product.'.token')
			->where('langs_id', Session::get('language'))
			->where($product.'.token', $token)
			->leftJoinSub($compositions, $as='c', $product.'.token', $operator = '=', $second = 'c.token')
			->select([
				$product.'.token',
				'c.token as composition',
				$product.'.images_resized',
				$product_names.'.name as name',
				$product_names.'.slug as slug',
				$product_names.'.intro as intro',
				$product_names.'.meta_keywords as meta_keywords',
				$product_names.'.meta_description as meta_description',
				$product_names.'.og_title as og_title',
				$product_names.'.og_type as og_type',
				$product_names.'.og_url as og_url',
				$product_names.'.og_image as og_image',
				$product_names.'.og_description as og_description',
				$product_names.'.created_at as created_at',
				$product_names.'.updated_at as updated_at',
			])
			->first();
		$resultset['product_description'] = DB::table($product_description_content)
			->where('langs_id', Session::get('language'))
			->where($product_description_content.'.token', $token)
			->get();
		return $resultset;
	}
}

// Session::get('language')