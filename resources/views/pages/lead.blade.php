@extends('layouts.app')

@section('meta_title', config('marketing.lead.meta_title'))
@section('meta_description', config('marketing.lead.meta_description'))
@section('meta_keywords', config('marketing.lead.meta_keywords'))
@section('og_title', config('marketing.lead.meta_title'))
@section('og_description', config('marketing.lead.meta_description'))
@section('meta_robots', 'index, follow')

@section('content')
<section class="pt-8 pb-5">
	<div class="container">
		<div class="row g-5">
			<div class="col-lg-5">
				<h1 class="mb-3">Start Your Project with Artixcore</h1>
				<p class="lead text-muted">Tell us what you want to build. Our team will review your request and contact you with the next step.</p>
				<p class="text-muted small">Whether you need a SaaS platform, AI-powered software, a web application, an e-commerce system, or a custom business tool, share the details and we’ll help you shape the right solution.</p>
				@if($site->contact_email)
					<p class="mb-0 mt-4"><strong>Email:</strong> <a href="mailto:{{ $site->contact_email }}">{{ $site->contact_email }}</a></p>
				@endif
			</div>
			<div class="col-lg-7">
				@if (session('status'))
					<div class="alert alert-success" role="status">{{ session('status') }}</div>
				@endif

				<div id="lead-form-error" class="alert alert-danger d-none" role="alert"></div>

				<div id="lead-success" class="card border-0 shadow-sm mb-4 d-none">
					<div class="card-body p-4 p-md-5">
						<h2 class="h4 mb-3">Welcome, <span data-lead-success-name></span>!</h2>
						<p class="mb-3">Thank you for contacting Artixcore. We received your project request and will contact you at <span data-lead-success-email></span> soon.</p>
						<p class="text-muted small mb-4">Our team usually reviews new requests quickly. In the meantime, you can explore our services or return to the homepage.</p>
						<div class="d-flex flex-wrap gap-2">
							<a href="{{ route('services.index') }}" class="btn btn-primary mb-0">Explore Services</a>
							<a href="{{ route('home') }}" class="btn btn-outline-secondary mb-0">Back to Home</a>
						</div>
					</div>
				</div>

				<form id="lead-form" method="post" action="{{ route('lead.store') }}" novalidate>
					@csrf
					<input type="hidden" name="source" value="{{ old('source', 'website') }}">

					<div class="position-absolute opacity-0 overflow-hidden" style="width:1px;height:1px;" aria-hidden="true">
						<label for="website">Leave blank</label>
						<input type="text" name="website" id="website" tabindex="-1" autocomplete="off" value="">
					</div>

					<div class="mb-3">
						<label class="form-label" for="lead-name">Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control @error('name') is-invalid @enderror" id="lead-name" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name" aria-describedby="err-name">
						<div id="err-name" class="text-danger small mt-1 {{ $errors->has('name') ? '' : 'd-none' }}" data-error-for="name" role="alert">@if($errors->has('name')){{ $errors->first('name') }}@endif</div>
					</div>
					<div class="mb-3">
						<label class="form-label" for="lead-email">Email <span class="text-danger">*</span></label>
						<input type="email" class="form-control @error('email') is-invalid @enderror" id="lead-email" name="email" value="{{ old('email') }}" required maxlength="190" autocomplete="email" aria-describedby="err-email">
						<div id="err-email" class="text-danger small mt-1 {{ $errors->has('email') ? '' : 'd-none' }}" data-error-for="email" role="alert">@if($errors->has('email')){{ $errors->first('email') }}@endif</div>
					</div>
					<div class="mb-3">
						<label class="form-label" for="lead-phone">Phone</label>
						<input type="text" class="form-control @error('phone') is-invalid @enderror" id="lead-phone" name="phone" value="{{ old('phone') }}" maxlength="40" autocomplete="tel" aria-describedby="err-phone">
						<div id="err-phone" class="text-danger small mt-1 {{ $errors->has('phone') ? '' : 'd-none' }}" data-error-for="phone" role="alert">@if($errors->has('phone')){{ $errors->first('phone') }}@endif</div>
					</div>
					<div class="mb-3">
						<label class="form-label" for="service_type">Service type <span class="text-danger">*</span></label>
						<select class="form-select @error('service_type') is-invalid @enderror" id="service_type" name="service_type" required aria-describedby="err-service_type">
							<option value="" disabled @selected(!old('service_type'))>Select a service</option>
							@foreach (\App\Models\Lead::SERVICE_TYPES as $opt)
								<option value="{{ $opt }}" @selected(old('service_type') === $opt)>{{ $opt }}</option>
							@endforeach
						</select>
						<div id="err-service_type" class="text-danger small mt-1 {{ $errors->has('service_type') ? '' : 'd-none' }}" data-error-for="service_type" role="alert">@if($errors->has('service_type')){{ $errors->first('service_type') }}@endif</div>
					</div>
					<div class="mb-3">
						<label class="form-label" for="lead-message">Message <span class="text-danger">*</span></label>
						<textarea class="form-control @error('message') is-invalid @enderror" id="lead-message" name="message" rows="6" required minlength="10" maxlength="5000" placeholder="Describe your goals, timeline, and any technical preferences." aria-describedby="err-message">{{ old('message') }}</textarea>
						<div id="err-message" class="text-danger small mt-1 {{ $errors->has('message') ? '' : 'd-none' }}" data-error-for="message" role="alert">@if($errors->has('message')){{ $errors->first('message') }}@endif</div>
					</div>

					@if (! $captchaBypass)
						<div class="mb-3">
							<span class="form-label d-block" id="lead-captcha-label">Verification <span class="text-danger">*</span></span>
							@if ($captchaDriver === 'turnstile' && $turnstileSiteKey)
								<div id="lead-captcha" data-sitekey="{{ $turnstileSiteKey }}" data-theme="light"></div>
							@elseif ($captchaDriver === 'recaptcha_v2' && $recaptchaSiteKey)
								<div id="lead-captcha" class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
							@else
								<p class="text-warning small mb-0">Captcha is not fully configured. Set site and secret keys in your environment.</p>
							@endif
							@php
								$captchaErr = $errors->has('captcha') ? $errors->first('captcha') : ($errors->has('cf-turnstile-response') ? $errors->first('cf-turnstile-response') : ($errors->has('g-recaptcha-response') ? $errors->first('g-recaptcha-response') : null));
							@endphp
							<div class="text-danger small mt-2 {{ $captchaErr ? '' : 'd-none' }}" data-error-for="captcha" role="alert" aria-labelledby="lead-captcha-label">{{ $captchaErr ?? '' }}</div>
						</div>
					@endif

					<button type="submit" id="lead-submit" class="btn btn-primary mb-0" data-label-default="Submit Project Request">
						<span class="lead-submit-label">Submit Project Request</span>
						<span class="spinner-border spinner-border-sm ms-1 d-none align-middle" data-lead-submit-spinner role="status" aria-hidden="true"></span>
					</button>
				</form>
			</div>
		</div>
	</div>
</section>
@endsection

@push('scripts')
@if (! $captchaBypass)
	@if ($captchaDriver === 'turnstile' && $turnstileSiteKey)
		<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
	@elseif ($captchaDriver === 'recaptcha_v2' && $recaptchaSiteKey)
		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	@endif
@endif
@vite(['resources/js/lead.js'])
@endpush
