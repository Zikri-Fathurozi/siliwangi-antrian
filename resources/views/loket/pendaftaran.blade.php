@extends('layouts.app')

@section('content')
	<loket-pendaftaran-component
		:id_loket="{{ Auth::user()->loket }}"
	></loket-pendaftaran-component>
@endsection
