@extends('layouts.app')

@section('title', '示例首页')
@section('example.index')
    {{ $content }}
@endsection

@section('script')
<script>
	new Vue({
		el: '#app',
		vuetify: new Vuetify(),
		data:{
        },
		created(){

		},
		watch:{
		},
	})
</script>
@endsection