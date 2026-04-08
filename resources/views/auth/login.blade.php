<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Admin login — {{ config('app.name') }}</title>
	<link rel="stylesheet" href="{{ asset('theme/vendor/bootstrap-icons/bootstrap-icons.css') }}">
	<link rel="stylesheet" href="{{ asset('theme/vendor/bootstrap/dist/css/bootstrap.min.css') }}">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-5">
				<div class="card shadow-sm border-0 mt-5">
					<div class="card-body p-4 p-md-5">
						<h1 class="h4 mb-4">Artixcore admin</h1>
						@if($errors->any())
							<div class="alert alert-danger small">{{ $errors->first() }}</div>
						@endif
						<form method="post" action="{{ route('login') }}">
							@csrf
							<div class="mb-3">
								<label class="form-label" for="email">Email</label>
								<input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
							</div>
							<div class="mb-3">
								<label class="form-label" for="password">Password</label>
								<input type="password" class="form-control" id="password" name="password" required>
							</div>
							<div class="mb-3 form-check">
								<input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
								<label class="form-check-label" for="remember">Remember me</label>
							</div>
							<button type="submit" class="btn btn-primary w-100">Sign in</button>
						</form>
						<p class="small text-muted mt-3 mb-0">Filament panel: <a href="{{ url('/filament') }}">/filament</a></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
