@extends('layouts.tools')

@section('content')
	<display-poli-component
		:app_name="'{{ config('antrian.header.name') }}'"
		:app_address="'{{ config('antrian.header.address') }}'"
		:gedung="{{ $gedung }}"
		:app_multiloket="'{{ config('antrian.muli_loket_poli') }}'"
	></display-poli-component>
@endsection
