<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="robots" content="noindex, nofollow, noarchive">
	<title>Artixcore Admin — Sign in</title>
	@vite(['resources/css/app.css', 'resources/js/auth-web.js'])
</head>
<body class="min-h-full bg-zinc-950 font-sans text-zinc-100 antialiased">
	<div class="flex min-h-full flex-col justify-center px-4 py-12 sm:px-6 lg:px-8">
		<div class="sm:mx-auto sm:w-full sm:max-w-md">
			<p class="text-center text-lg font-semibold tracking-tight text-white">Artixcore Admin</p>
			<p class="mt-1 text-center text-sm text-zinc-400">Authorized personnel only</p>
			<p class="mt-2 text-center text-xs text-amber-200/90">Access may be restricted by IP allowlist.</p>
		</div>

		<div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
			<div class="rounded-2xl border border-zinc-800 bg-zinc-900/90 px-6 py-8 shadow-xl sm:px-10">
				<div data-auth-banner class="{{ $errors->any() ? '' : 'hidden' }} mb-4 rounded-lg border border-red-500/40 bg-red-950/60 px-4 py-3 text-sm text-red-100" role="alert">{{ $errors->first() }}</div>

				<form method="post" action="{{ route('admin.login.submit') }}" class="space-y-6" data-ajax-form>
					@csrf
					<div>
						<label for="email" class="mb-2 block text-sm font-medium text-zinc-300">Email</label>
						<input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="username" autofocus class="block w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-white outline-none focus:border-amber-500/60 focus:ring-2 focus:ring-amber-500/30">
						<p class="mt-1 hidden text-sm text-red-300" data-field-error="email"></p>
					</div>
					<div>
						<label for="password" class="mb-2 block text-sm font-medium text-zinc-300">Password</label>
						<div class="relative">
							<input type="password" name="password" id="password" required autocomplete="current-password" class="block w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 pe-20 text-white outline-none focus:border-amber-500/60 focus:ring-2 focus:ring-amber-500/30">
							<button type="button" class="absolute end-2 top-1/2 -translate-y-1/2 rounded px-2 py-1 text-xs text-zinc-400 hover:bg-zinc-800" data-toggle-password="#password">Show</button>
						</div>
						<p class="mt-1 hidden text-sm text-red-300" data-field-error="password"></p>
					</div>
					<div class="flex items-center">
						<input type="checkbox" name="remember" id="remember" value="1" class="size-4 rounded border-zinc-600 bg-zinc-950 text-amber-500">
						<label for="remember" class="ms-2 text-sm text-zinc-400">Remember me</label>
					</div>
					<button type="submit" class="flex w-full justify-center rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-zinc-950 hover:bg-amber-400">Sign in</button>
				</form>

				<p class="mt-6 text-center text-xs text-zinc-500">
					<span class="inline-flex items-center gap-1 rounded border border-zinc-700 px-2 py-0.5 text-zinc-400">Secured session</span>
				</p>
			</div>
		</div>
	</div>
</body>
</html>
