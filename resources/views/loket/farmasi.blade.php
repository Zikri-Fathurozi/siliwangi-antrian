@extends('layouts.app')

@section('content')
	<loket-farmasi-component 
		:id_farmasi="{{ Auth::user()->farmasi}}"
	></loket-farmasi-component>
@endsection
