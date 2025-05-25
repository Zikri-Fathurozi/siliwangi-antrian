@extends('layouts.tools')

@section('content')
	<display-farmasi-component
		:app_name="'{{ config('antrian.header.name') }}'"
		:app_address="'{{ config('antrian.header.address') }}'"
		:poli="{{ $poli }}"
		:app_multiloket="'{{ config('antrian.muli_loket_poli') }}'"
	></display-farmasi-component>
@endsection
