<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="robots" content="noindex, nofollow, noarchive">
	<title>Create account — {{ config('app.name') }}</title>
	@vite(['resources/css/app.css', 'resources/js/auth-web.js'])
</head>
<body class="min-h-full bg-slate-50 font-sans text-slate-900 antialiased">
	<div class="flex min-h-full flex-col justify-center px-4 py-12 sm:px-6 lg:px-8">
		<div class="sm:mx-auto sm:w-full sm:max-w-md">
			<p class="text-center text-xl font-semibold tracking-tight text-slate-900">{{ config('app.name') }}</p>
			<p class="mt-1 text-center text-sm text-slate-600">Create your customer account to access the portal, track requests, and explore services.</p>
		</div>

		<div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
			<div class="rounded-2xl border border-slate-200 bg-white px-6 py-8 shadow-sm sm:px-10">
				<div
					data-auth-banner
					class="{{ $errors->any() ? '' : 'hidden' }} mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
					role="alert"
				>{{ $errors->first() }}</div>

				<form id="register-form" method="post" action="{{ route('register.submit') }}" class="space-y-5" data-ajax-form>
					@csrf
					<div>
						<label for="name" class="mb-2 block text-sm font-medium text-slate-700">Full name</label>
						<input
							type="text"
							name="name"
							id="name"
							value="{{ old('name') }}"
							required
							autocomplete="name"
							autofocus
							class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-slate-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
						>
						<p class="mt-1 hidden text-sm text-red-600" data-field-error="name"></p>
					</div>
					<div>
						<label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email</label>
						<input
							type="email"
							name="email"
							id="email"
							value="{{ old('email') }}"
							required
							autocomplete="email"
							class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-slate-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
						>
						<p class="mt-1 hidden text-sm text-red-600" data-field-error="email"></p>
					</div>
					<div>
						<label for="company_name" class="mb-2 block text-sm font-medium text-slate-700">Company <span class="font-normal text-slate-500">(optional)</span></label>
						<input
							type="text"
							name="company_name"
							id="company_name"
							value="{{ old('company_name') }}"
							autocomplete="organization"
							class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-slate-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
						>
						<p class="mt-1 hidden text-sm text-red-600" data-field-error="company_name"></p>
					</div>
					<div>
						<label for="phone" class="mb-2 block text-sm font-medium text-slate-700">Phone <span class="font-normal text-slate-500">(optional)</span></label>
						<input
							type="tel"
							name="phone"
							id="phone"
							value="{{ old('phone') }}"
							autocomplete="tel"
							class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-slate-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
						>
						<p class="mt-1 hidden text-sm text-red-600" data-field-error="phone"></p>
					</div>
					<div>
						<label for="password" class="mb-2 block text-sm font-medium text-slate-700">Password</label>
						<div class="relative">
							<input
								type="password"
								name="password"
								id="password"
								required
								autocomplete="new-password"
								class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 pe-24 text-slate-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
							>
							<button
								type="button"
								class="absolute end-2 top-1/2 -translate-y-1/2 rounded px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100"
								data-toggle-password="#password"
							>Show</button>
						</div>
						<p class="mt-1 hidden text-sm text-red-600" data-field-error="password"></p>
					</div>
					<div>
						<label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">Confirm password</label>
						<div class="relative">
							<input
								type="password"
								name="password_confirmation"
								id="password_confirmation"
								required
								autocomplete="new-password"
								class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 pe-24 text-slate-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
							>
							<button
								type="button"
								class="absolute end-2 top-1/2 -translate-y-1/2 rounded px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100"
								data-toggle-password="#password_confirmation"
							>Show</button>
						</div>
						<p class="mt-1 hidden text-sm text-red-600" data-field-error="password_confirmation"></p>
					</div>
					<div class="flex items-start gap-2">
						<input type="checkbox" name="terms" id="terms" value="1" class="mt-1 size-4 rounded border-slate-300 text-indigo-600" required>
						<label for="terms" class="text-sm text-slate-600">
							I agree to the <a href="{{ route('terms') }}" class="font-medium text-indigo-600 hover:text-indigo-500" target="_blank" rel="noopener">terms and conditions</a>.
						</label>
					</div>
					<p class="mt-1 hidden text-sm text-red-600" data-field-error="terms"></p>
					<button
						type="submit"
						class="flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-500"
					>Create account</button>
				</form>

				<p class="mt-6 text-center text-sm text-slate-600">
					Already have an account?
					<a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Sign in</a>
				</p>
				<p class="mt-2 text-center text-sm text-slate-600">
					<a href="{{ route('home') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Back to home</a>
				</p>
			</div>
		</div>
	</div>
</body>
</html>
