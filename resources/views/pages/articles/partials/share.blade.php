@php
	$url = urlencode($url ?? url()->current());
	$text = urlencode($title ?? '');
@endphp
<div class="d-flex flex-wrap gap-2 align-items-center">
	<span class="small text-muted me-1">Share:</span>
	<a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $url }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener noreferrer">LinkedIn</a>
	<a href="https://twitter.com/intent/tweet?url={{ $url }}&text={{ $text }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener noreferrer">X</a>
	<a href="https://www.facebook.com/sharer/sharer.php?u={{ $url }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener noreferrer">Facebook</a>
</div>
