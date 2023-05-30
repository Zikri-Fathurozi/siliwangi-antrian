@extends('layouts.tools')

@section('content')
	<display-kamar-component
		:app_name="'{{ config('antrian.header.name') }}'"
		:app_address="'{{ config('antrian.header.address') }}'"
		:gedung="{{ $gedung }}"
        :poli_id="{{ $poli }}"
		:app_multiloket="'{{ config('antrian.muli_loket_poli') }}'"
	></display-kamar-component>
@endsection
