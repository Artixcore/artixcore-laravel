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
				<form method="post" action="{{ route('lead.store') }}" novalidate class="needs-validation">
					@csrf
					<input type="hidden" name="source" value="{{ old('source', 'website') }}">

					<div class="position-absolute opacity-0 overflow-hidden" style="width:1px;height:1px;" aria-hidden="true">
						<label for="website">Leave blank</label>
						<input type="text" name="website" id="website" tabindex="-1" autocomplete="off" value="">
					</div>

					<div class="mb-3">
						<label class="form-label" for="lead-name">Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control @error('name') is-invalid @enderror" id="lead-name" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name">
						@error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
					</div>
					<div class="mb-3">
						<label class="form-label" for="lead-email">Email <span class="text-danger">*</span></label>
						<input type="email" class="form-control @error('email') is-invalid @enderror" id="lead-email" name="email" value="{{ old('email') }}" required maxlength="190" autocomplete="email">
						@error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
					</div>
					<div class="mb-3">
						<label class="form-label" for="lead-phone">Phone</label>
						<input type="text" class="form-control @error('phone') is-invalid @enderror" id="lead-phone" name="phone" value="{{ old('phone') }}" maxlength="40" autocomplete="tel">
						@error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
					</div>
					<div class="mb-3">
						<label class="form-label" for="service_type">Service type <span class="text-danger">*</span></label>
						<select class="form-select @error('service_type') is-invalid @enderror" id="service_type" name="service_type" required>
							<option value="" disabled @selected(!old('service_type'))>Select a service</option>
							@foreach (\App\Models\Lead::SERVICE_TYPES as $opt)
								<option value="{{ $opt }}" @selected(old('service_type') === $opt)>{{ $opt }}</option>
							@endforeach
						</select>
						@error('service_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
					</div>
					<div class="mb-3">
						<label class="form-label" for="lead-message">Message <span class="text-danger">*</span></label>
						<textarea class="form-control @error('message') is-invalid @enderror" id="lead-message" name="message" rows="6" required minlength="10" maxlength="5000" placeholder="Describe your goals, timeline, and any technical preferences.">{{ old('message') }}</textarea>
						@error('message')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
					</div>

					@if (! $captchaBypass)
						<div class="mb-3">
							<span class="form-label d-block">Verification <span class="text-danger">*</span></span>
							@if ($captchaDriver === 'turnstile' && $turnstileSiteKey)
								<div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}"></div>
							@elseif ($captchaDriver === 'recaptcha_v2' && $recaptchaSiteKey)
								<div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
							@else
								<p class="text-warning small mb-0">Captcha is not fully configured. Set site and secret keys in your environment.</p>
							@endif
							@error('captcha')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
						</div>
					@endif

					<button type="submit" class="btn btn-primary mb-0">Submit Project Request</button>
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
@endpush
