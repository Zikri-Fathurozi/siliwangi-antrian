<?php
$roles = \App\Role::select(["role_nama", "role_id"])->pluck(
  "role_nama",
  "role_id"
); ?>

<a href="#" class="nav-link pr-0 leading-none" data-toggle="dropdown">
<span class="avatar" style="background-image: url({{asset('user.png')}})"></span>
<span class="ml-2 d-none d-lg-block">
  <span class="text-default">@if(Auth::check()) {{ Auth::user()->name }} @endif</span>	
  <small class="text-muted d-block mt-1"><b>{{ $roles[Auth::user()->role] }}</b></small>
</span>
</a>
<div class="dropdown-menu cstm_dropdown dropdown-menu-right dropdown-menu-arrow">
	<a class="dropdown-item" href="{{ route('setting.profile') }}">
	  <i class="dropdown-icon fa fa-user"></i> Profile
	</a>
	<div class="dropdown-divider"></div>
	<a class="dropdown-item" href="{{ route('logout') }}"
	   onclick="event.preventDefault();document.getElementById('logout-form').submit();">
		<i class="dropdown-icon fa fa-sign-out"></i>  Logout
	</a>
	<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
		@csrf
	</form>
</div>
