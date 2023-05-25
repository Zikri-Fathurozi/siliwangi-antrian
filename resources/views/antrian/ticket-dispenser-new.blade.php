@extends('layouts.tools')

@section('content')
	<ticket-dispenser-new-component
		:app_name="'{{ config('antrian.header.name') }}'"
		:app_address="'{{ config('antrian.header.address') }}'"
	></ticket-dispenser-new-component>
@endsection