@extends('layouts.app')

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