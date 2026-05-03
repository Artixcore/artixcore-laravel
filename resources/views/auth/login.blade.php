<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>Admin login — {{ config('app.name') }}</title>
	@vite(['resources/css/app.css'])
</head>
<body class="min-h-full bg-slate-950 bg-[radial-gradient(ellipse_120%_80%_at_50%_-20%,rgba(251,191,36,0.12),transparent)] font-sans text-slate-100 antialiased">
	<div class="flex min-h-full flex-col justify-center px-4 py-12 sm:px-6 lg:px-8">
		<div class="sm:mx-auto sm:w-full sm:max-w-md">
			<p class="text-center text-lg font-semibold tracking-tight text-white">{{ config('app.name') }}</p>
			<p class="mt-1 text-center text-sm text-slate-400">Blade admin sign-in</p>
		</div>

		<div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
			<div class="rounded-2xl border border-white/10 bg-slate-900/80 px-6 py-8 shadow-xl shadow-black/40 backdrop-blur-sm sm:px-10">
				@if ($errors->any())
					<div class="mb-6 rounded-lg border border-red-500/30 bg-red-950/50 px-4 py-3 text-sm text-red-200" role="alert">
						{{ $errors->first() }}
					</div>
				@endif

				<form method="post" action="{{ route('login') }}" class="space-y-6">
					@csrf
					<div>
						<label for="email" class="mb-2 block text-sm font-medium text-slate-300">Email</label>
						<input
							type="email"
							name="email"
							id="email"
							value="{{ old('email') }}"
							required
							autocomplete="email"
							autofocus
							class="block w-full rounded-lg border border-white/10 bg-slate-950/80 px-3 py-2.5 text-white placeholder:text-slate-500 shadow-inner outline-none ring-amber-500/0 transition focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/30"
							placeholder="you@company.com"
						>
					</div>
					<div>
						<label for="password" class="mb-2 block text-sm font-medium text-slate-300">Password</label>
						<input
							type="password"
							name="password"
							id="password"
							required
							autocomplete="current-password"
							class="block w-full rounded-lg border border-white/10 bg-slate-950/80 px-3 py-2.5 text-white placeholder:text-slate-500 shadow-inner outline-none ring-amber-500/0 transition focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/30"
						>
					</div>
					<div class="flex items-center">
						<input
							type="checkbox"
							name="remember"
							id="remember"
							value="1"
							class="size-4 rounded border-white/20 bg-slate-950 text-amber-500 focus:ring-amber-500/40"
							{{ old('remember') ? 'checked' : '' }}
						>
						<label for="remember" class="ms-2 block text-sm text-slate-400">Remember me</label>
					</div>
					<button
						type="submit"
						class="flex w-full justify-center rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900"
					>
						Sign in
					</button>
				</form>

				<p class="mt-6 text-center text-xs text-slate-500">
					<a href="{{ route('home') }}" class="font-medium text-amber-400/90 underline-offset-2 hover:text-amber-300 hover:underline">Back to site</a>
				</p>
			</div>
		</div>
	</div>
</body>
</html>
