@php
	$p = $saasPage;
	$items = $p['why_items'] ?? [];
	$items = is_array($items) ? $items : [];
@endphp
@if(!empty($p['why_title']) && count($items) > 0)
<section class="py-5 overflow-hidden">
	<div class="container">
		<div class="row align-items-center g-4 g-lg-5">
			<div class="col-lg-6 position-relative pe-lg-7 order-2 order-lg-1" data-aos="fade-right">
				<figure class="position-absolute top-50 start-50 translate-middle d-none d-lg-block">
					<svg class="fill-light" width="451" height="374" viewBox="0 0 451 374" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M447.529 122.679C466.73 187.993 403.737 260.438 321.205 311.776C238.337 363.4 135.594 393.918 71.59 359.122C7.24919 324.326 -18.3524 224.501 13.9864 143.785C45.9884 63.069 136.268 1.46255 231.6 0.0364779C327.269 -1.67481 428.327 57.0795 447.529 122.679Z"/>
					</svg>
				</figure>
				<div class="row justify-content-center position-relative">
					<div class="col-9">
						<img src="{{ asset('theme/images/elements/saas-decoration/decoration-2.png') }}" class="img-fluid mb-4" alt="">
					</div>
					<div class="col-5">
						<img src="{{ asset('theme/images/elements/saas-decoration/17.png') }}" class="img-fluid" alt="">
					</div>
					<div class="col-5 me-auto">
						<img src="{{ asset('theme/images/elements/saas-decoration/16.png') }}" class="img-fluid rounded-3 shadow" alt="">
					</div>
				</div>
			</div>
			<div class="col-lg-6 z-index-2 order-1 order-lg-2" data-aos="fade-left">
				<h2 class="mb-4 mb-lg-5">{{ $p['why_title'] }}</h2>
				<div class="row g-4">
					@foreach($items as $i => $item)
						<div class="col-sm-6">
							<div class="icon-lg rounded-circle heading-color bg-light mb-3">
								<i class="bi bi-check-lg fa-lg"></i>
							</div>
							<h6 class="mb-2">{{ $item['title'] ?? '' }}</h6>
							<p class="mb-0 small text-muted">{{ $item['body'] ?? '' }}</p>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</section>
@endif
