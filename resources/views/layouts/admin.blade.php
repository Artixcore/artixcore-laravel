<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="{{ asset('theme/vendor/bootstrap-icons/bootstrap-icons.css') }}">
	<style>
		.admin-sidebar { min-height: 100vh; border-right: 1px solid var(--bs-border-color); }
		#admin-toast { position: fixed; top: 1rem; right: 1rem; z-index: 1080; min-width: 240px; }
	</style>
	@stack('styles')
</head>
<body class="bg-light">
<div id="admin-toast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
	<div class="d-flex">
		<div class="toast-body" id="admin-toast-body"></div>
		<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
	</div>
</div>
<div class="container-fluid">
	<div class="row">
		@include('admin.partials.sidebar')
		<main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
			@if(session('status'))
				<div class="alert alert-success py-2 small">{{ session('status') }}</div>
			@endif
			@yield('content')
		</main>
	</div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="{{ asset('theme/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script>
(function () {
	var token = document.querySelector('meta[name="csrf-token"]');
	if (token) {
		$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token.getAttribute('content') } });
	}
	window.adminToast = function (message, type) {
		var el = document.getElementById('admin-toast');
		var body = document.getElementById('admin-toast-body');
		if (!el || !body) return;
		el.classList.remove('text-bg-success', 'text-bg-danger', 'text-bg-warning');
		el.classList.add(type === 'error' ? 'text-bg-danger' : 'text-bg-success');
		body.textContent = message;
		var t = new bootstrap.Toast(el, { delay: 4000 });
		t.show();
	};
	$(document).on('click', '[data-admin-delete]', function (e) {
		e.preventDefault();
		if (!confirm('Delete this record?')) return;
		var url = $(this).data('admin-delete');
		$.ajax({
			url: url,
			type: 'POST',
			data: { _method: 'DELETE', _token: $('meta[name="csrf-token"]').attr('content') },
			headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
			success: function (res) {
				adminToast(res.message || 'Deleted.', 'success');
				$(e.currentTarget).closest('[data-admin-row]').remove();
			},
			error: function () {
				adminToast('Could not delete.', 'error');
			}
		});
	});
})();
</script>
@stack('scripts')
</body>
</html>
