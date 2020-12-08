<!DOCTYPE html>
<html lang="pl-PL">
<?php (!isset($head))?$head = zmcms_get_initial_head_data($theme = Config('frontend.theme_name')):null; ?>
	<head>	
	@include('themes.'.Config('zmcms.frontend.theme_name').'.frontend.zmcms_html_header')
	@includeIf('themes.'.Config('frontend.theme_name').'.seo.google_script')
	</head>
<body>
<a href="/">
<div class="logo">	
<img src="{{ '/'.Config(Config('zmcms.frontend.theme_name').'.media.logo') }}" alt="{{ Config(Config('zmcms.frontend.theme_name').'.contact_data.headquarters.business_name') }}" >
</div>
</a>
<nav id="main">
		<div class="mobile_control">
			<a href="" id="btn_phone" title="{{___('Zadzwoń')}}"><span class="fas fa-phone-square"></span></a>
			<a href="" id="btn_bars" title="{{___('Otwórz menu')}}"><span class="fas fa-bars"></span></a>
			<a href="" id="btn_times" class="hidden" title="{{___('Zamknij menu')}}"><span class="fas fa-times"></span></a>
		</div>
</nav>
<nav id="main_positions" class="mobile_hide">
		<ul id="mnu_main">{{zmcms_website_navigations_frontend($position = 'main', $parent = null, $to_file=false)}}</ul>
</nav>
	<header style="background-image: url({{(json_decode($data['navigation']->images_resized, true)['ilustration']['1400'])}})">
	{{-- <header> --}}
	<div class="color_filter">
		<h1>{{$data['product']['product']->name}}</h1>
		@if(strlen($data['product']['product']->og_description)>3)
			<div class="og_description">{{$data['product']['product']->og_description}}</div>
		@endif
	</div>
	</header>
	<content>
		<article>
			@if(isset($data['product_composition']) && count($data['product_composition'])>0)
			@if(isset($data['product']['product']->images_resized) && strlen(json_decode($data['navigation']->images_resized, true)['ilustration']['600'])>0)
			<div class="product_image">
				<a href="#">
					<img src="{{json_decode($data['navigation']->images_resized, true)['ilustration']['600']}}" alt="{{$data['product']['product']->name}}">
				</a>
			</div>
			@endif
			
			<div class="product_parameters pricelist">
				@foreach($data['product_composition'] as $r)
				<div class="parameter">
					<div class="c1">{{$r['composition_name']}}</div>
					<div class="c2">{{$r['price_brut']}}</div>
					<div class="c3">{{$r['vat']}}</div>
				</div>
				@endforeach	
			</div>
			
			<div class="description">
			{!! $data['product']['product']->intro !!}
			@foreach($data['product']['product_description'] as $r)
				{!! $r->description !!}
			@endforeach
			</div>
			@endif
			{{-- <pre>{{print_r($data, true)}}</pre> --}}
			
		</article>
		<aside>
			@includeIf('themes.'.Config('zmcms.frontend.theme_name').'.frontend.zmcms_main_contact_box')
		</aside>
		
	</content>
	@include('themes.'.(Config('zmcms.frontend.theme_name').'.frontend.zmcms_google_map'))
	@include('themes.'.(Config('zmcms.frontend.theme_name').'.frontend.footer'))

{!! zmcms_html_js('themes/'.Config('zmcms.frontend.theme_name').'/frontend/js', false) !!}
@stack('custom_js')
@include('themes.'.Config('zmcms.frontend.theme_name').'.frontend.zmcms_main_ajax_dialog_box')
</body>
</html>