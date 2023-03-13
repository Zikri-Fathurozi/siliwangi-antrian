@extends('layouts.app')

@section('content')
	<loket-poli-component 
		:id_poli="{{ Auth::user()->poli}}"
	></loket-poli-component>
@endsection
