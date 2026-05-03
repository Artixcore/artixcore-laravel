<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>{{ $emailSubject }}</title>
</head>
<body style="font-family: system-ui, sans-serif; line-height: 1.5; color: #18181b;">
	<p style="white-space: pre-wrap;">{!! nl2br(e($emailBody)) !!}</p>
</body>
</html>
