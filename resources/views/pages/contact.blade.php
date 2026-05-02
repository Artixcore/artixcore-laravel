@extends('layouts.app')

@section('meta_title', config('marketing.contact.meta_title'))
@section('meta_description', config('marketing.contact.meta_description'))
@section('og_title', config('marketing.contact.meta_title'))
@section('og_description', config('marketing.contact.meta_description'))

@section('content')
<section class="pt-8 pb-5">
	<div class="container">
		<div class="row g-5">
			<div class="col-lg-5">
				<h1 class="mb-3">Contact</h1>
				<p class="text-muted">Tell us about your project. We typically reply within two business days.</p>
				@if($site->contact_email)
					<p class="mb-0"><strong>Email:</strong> <a href="mailto:{{ $site->contact_email }}">{{ $site->contact_email }}</a></p>
				@endif
			</div>
			<div class="col-lg-7">
				<div id="contact-alert" class="alert d-none" role="alert"></div>
				@if(session('status'))
					<div class="alert alert-success">{{ session('status') }}</div>
				@endif
				<form id="contact-form" method="post" action="{{ route('contact.store') }}" novalidate>
					@csrf
					<div class="mb-3">
						<label class="form-label" for="name">Name</label>
						<input type="text" class="form-control" id="name" name="name" required>
					</div>
					<div class="mb-3">
						<label class="form-label" for="email">Email</label>
						<input type="email" class="form-control" id="email" name="email" required>
					</div>
					<div class="mb-3">
						<label class="form-label" for="company">Company</label>
						<input type="text" class="form-control" id="company" name="company">
					</div>
					<div class="mb-3">
						<label class="form-label" for="phone">Phone</label>
						<input type="text" class="form-control" id="phone" name="phone">
					</div>
					<div class="mb-3">
						<label class="form-label" for="message">Message</label>
						<textarea class="form-control" id="message" name="message" rows="5" required></textarea>
					</div>
					<button type="submit" class="btn btn-primary mb-0" id="contact-submit">Send message</button>
				</form>
			</div>
		</div>
	</div>
</section>
@endsection

@push('scripts')
<script>
(function () {
	var form = document.getElementById('contact-form');
	if (!form) return;
	var alertEl = document.getElementById('contact-alert');
	var submitBtn = document.getElementById('contact-submit');
	form.addEventListener('submit', function (e) {
		e.preventDefault();
		alertEl.classList.add('d-none');
		submitBtn.disabled = true;
		$.ajax({
			url: form.action,
			method: 'POST',
			data: $(form).serialize(),
			headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
			success: function (res) {
				alertEl.textContent = res.message || 'Sent.';
				alertEl.className = 'alert alert-success';
				alertEl.classList.remove('d-none');
				form.reset();
			},
			error: function (xhr) {
				var msg = 'Something went wrong.';
				if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
				if (xhr.responseJSON && xhr.responseJSON.errors) {
					msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
				}
				alertEl.textContent = msg;
				alertEl.className = 'alert alert-danger';
				alertEl.classList.remove('d-none');
			},
			complete: function () {
				submitBtn.disabled = false;
			}
		});
	});
})();
</script>
@endpush
