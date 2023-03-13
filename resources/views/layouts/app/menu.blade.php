<?php

use Illuminate\Support\Facades\Auth;

$role = Auth::user()->role;

if ($role == "poli") {
  $poli = \App\Poli::select(["poli_nama", "poli_id"])->pluck(
    "poli_nama",
    "poli_id"
  );
  $poli_nama = $poli[Auth::user()->poli];
}
?>
<div class="header collapse d-lg-flex p-0" id="headerMenuCollapse">
  <div class="container">
	<div class="row align-items-center">
	  <div class="col-lg-3 ml-auto text-right pr-4">
		<b><time-component></time-component></b>
	  </div>
	  <div class="col-lg order-lg-first">
		<ul class="nav nav-tabs border-0 flex-column flex-lg-row">

			@if($role == 'pendaftaran')
			<li class="nav-item">
				<a href="{{ route('home') }}" class="nav-link"><i class="fa fa-home"></i> LOKET&nbsp;<b>{{ config('antrian.muli_loket_pendaftaran')? Auth::user()->loket : 'PENDAFTARAN' }} {{ Auth::user()->prioritas? 'PRIORITAS' : '' }}</b></a>
			</li>

			@elseif($role == 'poli')
			<li class="nav-item">
				<a href="{{ route('loket.poli') }}" class="nav-link"><i class="fa fa-home"></i> LOKET&nbsp;<b>{{ isset($poli)? $poli_nama : '' }} {{ Auth::user()->prioritas? 'PRIORITAS' : '' }}</b></a>
			</li>

			@elseif($role == 'tensi')
			<li class="nav-item">
				<a href="{{ route('loket.tensi') }}" class="nav-link"><i class="fa fa-home"></i> LOKET&nbsp;<b>{{ isset($poli)? $poli_nama : '' }} {{ Auth::user()->prioritas? 'PRIORITAS' : '' }}</b></a>
			</li>

		  	@elseif($role == 'admin' || $role == 'superadmin')
			<li class="nav-item">
				<a href="{{ route('setting') }}" class="nav-link {{ request()->is('setting') ? 'active' : '' }}"><i class="fa fa-home"></i> Home</a>
		  	</li>
			
			 <li class="nav-item">
				<a href="javascript:void(0)" class="nav-link {{ request()->is('report/*') ? 'active' : '' }}" data-toggle="dropdown"><i class="fa fa-bar-chart"></i> Laporan</a>
				<div class="dropdown-menu dropdown-menu-arrow">
					<a href="{{ route('report.visit') }}" class="dropdown-item ">Kunjungan Harian</a>
					<a href="{{ route('report.rujukan') }}" class="dropdown-item ">Rujukan Harian</a>
					<a href="{{ route('report.antrian') }}" class="dropdown-item ">Antrian</a>
					<a href="{{ route('report.pasien') }}" class="dropdown-item ">Pasien JKN</a>
				</div>
			</li>

			<li class="nav-item">
				<a href="javascript:void(0)" class="nav-link {{ request()->is('master/*') ? 'active' : '' }}" data-toggle="dropdown"><i class="fa fa-shield"></i> Master</a>
				<div class="dropdown-menu dropdown-menu-arrow">
					<a href="{{ route('master.users') }}" class="dropdown-item ">Petugas</a>
					<a href="{{ route('master.poli') }}" class="dropdown-item ">Poli</a>
					@if($role == 'superadmin')
						<a href="{{ route('master.channel') }}" class="dropdown-item ">Channel</a>
					@endif
				</div>
			</li>
			<li class="nav-item">
				<a href="javascript:void(0)" class="nav-link {{ request()->is('display/*') ? 'active' : '' }}" data-toggle="dropdown"><i class="fa fa-television"></i> Display</a>
				<div class="dropdown-menu dropdown-menu-arrow">
					<a href="{{ route('display.info') }}" class="dropdown-item ">Info Text</a>
					<a href="{{ route('display.banner') }}" class="dropdown-item ">Banner</a>					
				</div>
			</li>
			<li class="nav-item">
				<a href="javascript:void(0)" class="nav-link {{ request()->is('setting/*') ? 'active' : '' }}" data-toggle="dropdown"><i class="fa fa-gear"></i> Pengaturan</a>
				<div class="dropdown-menu dropdown-menu-arrow">
					<a href="{{ route('setting.printer') }}" class="dropdown-item ">Printer</a>
					<a href="{{ route('setting.device') }}" class="dropdown-item ">Device</a>
					@if($role == 'superadmin')
						<a href="{{ route('setting.service') }}" class="dropdown-item ">Service</a>
					@endif
				</div>
			</li>
			<li class="nav-item">
				<a href="{{ route('refresh.browser') }}" class="nav-link {{ request()->is('refresh.browser') ? 'active' : '' }}"><i class="fa fa-refresh"></i> Refresh Browser</a>				
			</li>
			@endif

		</ul>
	  </div>
	</div>
  </div>
</div>
