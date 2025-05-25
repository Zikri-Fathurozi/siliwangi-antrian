@extends('layouts.app')

@section('content')
	<loket-farmasi-component 
		:id_poli="{{ Auth::user()->farmasi}}"
	></loket-farmasi-component>
@endsection
