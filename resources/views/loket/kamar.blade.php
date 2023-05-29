@extends('layouts.app')

@section('content')
    <p></p>
	<loket-kamar-component 
		:id_poli="{{ Auth::user()->poli}}"
        :nomor_kamar_urutan="{{ Auth::user()->nomor_kamar}}"
	></loket-kamar-component>
@endsection
