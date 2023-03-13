@extends('layouts.app')

@section('content')
	<loket-tensi-component 
		:id_tensi="{{ Auth::user()->tensi}}"
	></loket-tensi-component>
@endsection
