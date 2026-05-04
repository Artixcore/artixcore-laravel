<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="robots" content="noindex, nofollow, noarchive">
	<title>Master Admin Access</title>
	@vite(['resources/css/app.css', 'resources/js/auth-web.js'])
</head>
<body class="min-h-full bg-black font-sans text-zinc-100 antialiased">
	<div class="pointer-events-none fixed inset-0 bg-[radial-gradient(ellipse_at_top,_rgba(220,38,38,0.12),transparent_55%)]"></div>
	<div class="relative flex min-h-full flex-col justify-center px-4 py-12 sm:px-6 lg:px-8">
		<div class="sm:mx-auto sm:w-full sm:max-w-md">
			<p class="text-center text-lg font-bold tracking-tight text-red-500">Master Admin Access</p>
			<p class="mt-2 text-center text-sm text-red-200/80">High-security area — IP restricted</p>
			<p class="mt-3 text-center text-xs text-zinc-500">Unauthorized access is prohibited and may be prosecuted.</p>
		</div>

		<div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
			<div class="rounded-2xl border border-red-900/50 bg-zinc-950/90 px-6 py-8 shadow-2xl shadow-red-950/40 sm:px-10">
				<div data-auth-banner class="{{ $errors->any() ? '' : 'hidden' }} mb-4 rounded-lg border border-red-800 bg-red-950/80 px-4 py-3 text-sm text-red-100" role="alert">{{ $errors->first() }}</div>

				<form method="post" action="{{ route('master.login.submit') }}" class="space-y-6" data-auth-ajax="1">
					@csrf
					<div>
						<label for="email" class="mb-2 block text-sm font-medium text-zinc-300">Email</label>
						<input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="username" autofocus class="block w-full rounded-lg border border-zinc-800 bg-black px-3 py-2.5 text-white outline-none focus:border-red-500/70 focus:ring-2 focus:ring-red-500/30">
						<p class="mt-1 hidden text-sm text-red-300" data-field-error="email"></p>
					</div>
					<div>
						<label for="password" class="mb-2 block text-sm font-medium text-zinc-300">Password</label>
						<div class="relative">
							<input type="password" name="password" id="password" required autocomplete="current-password" class="block w-full rounded-lg border border-zinc-800 bg-black px-3 py-2.5 pe-20 text-white outline-none focus:border-red-500/70 focus:ring-2 focus:ring-red-500/30">
							<button type="button" class="absolute end-2 top-1/2 -translate-y-1/2 rounded px-2 py-1 text-xs text-zinc-400 hover:bg-zinc-900" data-toggle-password="#password">Show</button>
						</div>
						<p class="mt-1 hidden text-sm text-red-300" data-field-error="password"></p>
					</div>
					<div class="flex items-center">
						<input type="checkbox" name="remember" id="remember" value="1" class="size-4 rounded border-zinc-700 bg-black text-red-600">
						<label for="remember" class="ms-2 text-sm text-zinc-400">Remember me</label>
					</div>
					<button type="submit" class="flex w-full justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-500">Sign in</button>
				</form>
			</div>
		</div>
	</div>
	<script>
		document.addEventListener('click', (e) => {
			const b = e.target.closest('[data-toggle-password]');
			if (!b) return;
			const input = document.querySelector(b.getAttribute('data-toggle-password'));
			if (!input) return;
			input.type = input.type === 'password' ? 'text' : 'password';
			b.textContent = input.type === 'password' ? 'Show' : 'Hide';
		});
	</script>
</body>
</html>
