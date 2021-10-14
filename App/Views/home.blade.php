@extends('layouts.app')

@section('home')
<v-card outlind max-width="700" class="mx-auto">
  <v-card-text>
    <v-icon>mdi-account</v-icon>
    <v-text-field
      label="Filled"
      placeholder="Dense & Rounded"
      filled
      rounded
      dense
    ></v-text-field>
  </v-card-text>
  <!-- <v-icon>mdi-account</v-icon> -->
  
</v-card>
@endsection

@section('script')
<script>
	new Vue({
		el: '#app',
		vuetify: new Vuetify(),
		data:{
      publicData: {
        drawer: false,
        activeMenu: 'home',
        snackbar: false,
        snackbarColor: '',
        snackbarText: '',
      },

      privateData: {
        displayData:{
        },

        userData: {
          ws: null,
        },
      }
    },
		created(){
      this.initWebSocket();
		},
		watch:{
		},
	})
</script>
@endsection