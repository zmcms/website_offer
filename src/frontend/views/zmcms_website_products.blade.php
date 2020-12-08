@extends('themes.'.(Config('zmcms.frontend.theme_name') ?? 'zmcms').'.frontend.main')
@section('content')
@if(isset($data['navigation']) && (strlen($data['navigation']->ilustration)>0))
	<header style="background-image: url({{(json_decode($data['navigation']->images_resized, true)['ilustration']['1024'])}})">
		<div class="color_filter">
			<h1>{{$data['navigation']->name}}</h1>
			@if(strlen($data['navigation']->og_description)>0)<div class="og_description">{!! $data['navigation']->og_description !!}</div>@endif
		</div>
	</header>
@endif
<content>
<article>
		{!! $data['navigation']->content !!}
{{-- 		<div class="product_list">
		@foreach($data['products'] as $d)
			<div class="item">
				@if(isset(json_decode($d->images_resized, true)['ilustration']))
				<img src="{{(json_decode($d->images_resized, true)['ilustration']['200'])}}" alt="{{$d->name}}">
				@endif
				<a href="{{$data['navigation']->link}}/{{$d->slug}}">
					<h2>{{$d->name}}</h2>
					<div class="desc">
					{!! strip_tags($d->intro) !!} <i class="fas fa-angle-double-right"></i>
					</div>
				</a>
				<div class="b">
				@if(strlen($d->composition)>0)
					<span data-product_token="{{$d->token}}" data-run="/ajax_choose_product_version_frm/{{$d->token}}" class="control versions">od 15,00zł</span>
					<span data-product_token="{{$d->token}}" data-run="/ajax_choose_product_version_frm/{{$d->token}}" class="control versions fas fa-chevron-down"></span>
					<span data-product_token="{{$d->token}}" data-run="/ajax_add_to_cart/{{$d->token}}" class="control cart fas fa-shopping-cart"></span>
				@else
					<span style="width: 77%; text-align: center" data-product_token="{{$d->token}}" data-run="/ajax_add_to_cart" class="control cart ">15,00</span>
					<span style="width: 20%" data-product_token="{{$d->token}}" data-run="/ajax_add_to_cart/{{$d->token}}" class="control cart fas fa-shopping-cart"></span>
				@endif
				</div>
			</div>
			
		@endforeach
		</div>

 --}}

		<div class="items_lst">
		@foreach($data['products'] as $d)
		<a href="{{$data['navigation']->link}}/{{$d->slug}}">
		<div class="item">
			<div class="imgcontainer">
				<img src="{{(json_decode($d->images_resized, true)['ilustration']['200'])}}" alt="{{$d->name}}">
			</div>
			<h2>{{$d->name}}</h2>
			<div class="desc">
				{!! strip_tags($d->intro) !!} <i class="fas fa-angle-double-right"></i>
			</div>
		</div>
		</a>
		@endforeach
		</div>




		{!! $data['products']->links() !!}
		{{-- <pre>{{print_r($data, true)}}</pre> --}}
</article>
<aside>
	@includeIf('themes.'.Config('zmcms.frontend.theme_name').'.frontend.zmcms_main_contact_box')
</aside>
</content>
@include('themes.'.(Config('zmcms.frontend.theme_name').'.frontend.zmcms_google_map'))
@endsection
