@extends('layouts.auth')

@section('content')

<form method="POST" action="{{ route('login') }}" class="card">
	@csrf
	<div class="card-body p-6">
	  <div class="form-group">
		<label class="form-label">{{ __('E-Mail Address') }}</label>
		<input type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" aria-describedby="emailHelp" value="{{ old('email') }}" name="email" required autofocus placeholder="Enter email">
		
		@if ($errors->has('email'))
			<span class="invalid-feedback" role="alert">
				<strong>{{ $errors->first('email') }}</strong>
			</span>
		@endif
		
	  </div>
	  <div class="form-group">
		<label class="form-label">
		  {{ __('Password') }}
		  
		</label>
		<input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="Password" name="password" required>
		
		@if ($errors->has('password'))
			<span class="invalid-feedback" role="alert">
				<strong>{{ $errors->first('password') }}</strong>
			</span>
		@endif
	  </div>
	  <div class="form-group">
		<label class="custom-control custom-checkbox">
		  <input type="checkbox" class="custom-control-input form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
		  <span class="custom-control-label form-check-label">{{ __('Remember Me') }}</span>
		  
		</label>
	  </div>
	  <div class="form-footer">
		<button type="submit" class="btn btn-primary btn-block">
			{{ __('Login') }}
		</button>
	  </div>
	</div>
</form>
<div class="text-center text-muted">
<a href="{{ route('ticket.dispenser') }}" target="_blank" class="tag tag-rounded">Ticket Dispenser</a> -
<a href="{{ route('display.pendaftaran') }}" target="_blank" class="tag tag-rounded">Display Pendaftaran</a> -
<a href="{{ route('display.poli',['gedung'=>1]) }}" target="_blank" class="tag tag-rounded">Display Poli Gedung A</a> -
<a href="{{ route('display.poli',['gedung'=>2]) }}" target="_blank" class="tag tag-rounded">Display Poli Gedung B</a>
</div>
  
@endsection
