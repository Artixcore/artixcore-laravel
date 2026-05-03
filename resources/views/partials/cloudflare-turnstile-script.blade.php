@once('cloudflare-turnstile-api')
	@if (config('captcha.driver') === 'turnstile')
		@php
			$__turnstileSiteKey = (string) (config('services.turnstile.site_key') ?: config('captcha.turnstile.site_key', ''));
		@endphp
		@if ($__turnstileSiteKey !== '')
			<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
		@endif
	@endif
@endonce
