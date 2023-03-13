@extends('layouts.app')

@section('content')
	<loket-pendaftaran-component
		:id_loket="{{ config('antrian.multi_loket_pendaftaran')? Auth::user()->loket : '0'}}"
	></loket-pendaftaran-component>
@endsection
