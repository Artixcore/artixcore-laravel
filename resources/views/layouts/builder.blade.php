<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Builder — {{ $pageTitle }}</title>
    @vite(['resources/css/builder.css', 'resources/js/builder/main.jsx'])
</head>
<body class="builder-body antialiased text-slate-100">
    <div
        id="builder-root"
        data-page-id="{{ $pageId }}"
        data-page-title="{{ e($pageTitle) }}"
        data-page-path="{{ e($pagePath) }}"
    ></div>
</body>
</html>
