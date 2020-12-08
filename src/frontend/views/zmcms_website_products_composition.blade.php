@extends('themes.'.(Config('zmcms.frontend.theme_name') ?? 'zmcms').'.frontend.main')
@section('content')
<header class="document_header" style="background: url({{(json_decode($data['navigation']->images_resized, true)['ilustration']['1024'])}})">
	<h1>{{$data['products'][0]->name}}</h1>
</header>
<content>
<article>
		{!! $data['navigation']->content !!}
		<div class="product_config">
		<form id="product_config_frm" method="post" enctype="multipart/form-data">
		@foreach($data['products_composition'] as $d)
		<h1>{{$d['group_name']}}</h1>
			@if($d['group_choices']=='single')
				<select name="{{$d['group']}}">
					@foreach($d['data'] as $v)
					<option value="{{$v->token}}">{{$v->composition_name}}</option>
					@endforeach
				</select>
			@else
				@foreach($d['data'] as $v)
				<label>
					<input 
						type="checkbox" 
						name="{{$d['group']}}[]" 
						value="{{$v->token}}"
					>
					{{$v->name}}
				</label>
					@endforeach
				@endif
			
		@endforeach
		<button class="btn_submit">Wyślij</button>
		</form>
	</div>
</article>
<pre>
	{{print_r($data['products_composition'], true)}}
</pre>
<aside>
	@includeIf('themes.'.Config('zmcms.frontend.theme_name').'.frontend.zmcms_main_contact_box')
</aside>
</content>


@endsection
@push('custom_js')
    <script type="text/javascript">
	$('#product_config_frm .btn_submit').on('click', function(e){
		e.preventDefault();e.stopPropagation();
		f=$(this).parent().attr('id'); //ID FORMULARZA
		// alert(f);
		// return false;
		$('#ajax_dialog_box').fadeIn( "slow", function() {});
		$('#ajax_dialog_box_content').html('<div class="msg ok"><div class="loader"></div></div>');
		$.ajax({
			type: 'POST',
				url: "/website/frontend/product/add_to_cart",
				data: new FormData(document.getElementById(f)),
				processData: false,
				contentType: false,
				success: function(data){
					// alert(data);
					// var resultset = JSON.parse(data);
					// $('#ajax_dialog_box_content').html('<div class="msg '+resultset.result+'">'+resultset.code+': '+resultset.msg+'</div>');
					$('#ajax_dialog_box_content').html(data);
				},
				statusCode: {
					500: function(xhr) {$('#ajax_dialog_box_content').html('<div class="msg error">'+xhr.status+'<br>'+xhr.responseText+'</div>');},
					419: function(xhr){$('#ajax_dialog_box_content').html('<div class="msg error"><pre>'+xhr.responseText+'</pre></div>');},
					404: function(xhr){$('#ajax_dialog_box_content').html('<div class="msg error">Nie znaleziono skryptu</div>');},
					405: function(xhr){$('#ajax_dialog_box_content').html('<div class="msg error">'+xhr.status+'<br>'+xhr.responseText+'</div>');}
				}
		});
		return false;
	});
</script>
@endpush