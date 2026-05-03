<script src="{{ asset('theme/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('theme/vendor/ityped/index.js') }}"></script>
<script src="{{ asset('theme/vendor/swiper/swiper-bundle.min.js') }}"></script>
@stack('vendor_scripts')
<script src="{{ asset('theme/js/functions.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var collapseEl = document.getElementById('navbarCollapse');
	if (!collapseEl || typeof bootstrap === 'undefined' || !bootstrap.Collapse) {
		return;
	}
	var inst = bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false });
	collapseEl.querySelectorAll('a[href]:not(.dropdown-toggle)').forEach(function (link) {
		link.addEventListener('click', function () {
			var href = link.getAttribute('href');
			if (href && href !== '#' && window.matchMedia('(max-width: 1199.98px)').matches) {
				inst.hide();
			}
		});
	});
});
</script>
