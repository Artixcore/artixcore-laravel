<section class="pt-8 pb-0">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-8 text-center py-5">
				<h3>{{ $home['contact_teaser_title'] ?? "Let's talk" }}</h3>
				<p class="text-muted">{{ $home['contact_teaser_body'] ?? '' }}</p>
				<a href="{{ route('contact') }}" class="btn btn-primary mb-0">Contact us</a>
			</div>
		</div>
	</div>
</section>
