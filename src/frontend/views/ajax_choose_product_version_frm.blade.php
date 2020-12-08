<h3>{{$data['products']->name}}</h3>
@foreach($data['products_composition'] as $r)
<div class="ajax_product_version_picker">

@foreach($r['data'] as $p)
<div data-product_token="" class="item">
	<a href="#">
		{{$p->composition_name}}@if(strlen($p->price_brut)) - od {{$p->price_brut}} {{___('zł')}}@endif
	</a>
	<a href="#">
		<span class="fas fa-sliders-h"></span>
	</a>
	<a href="#">
		<span class="fas fa-shopping-cart"></span>
	</a>
</div>
@endforeach
</div>
@endforeach
{{-- <pre>{{print_r($data, true)}}</pre> --}}


{{-- <i class="fas fa-shopping-cart"></i> --}}