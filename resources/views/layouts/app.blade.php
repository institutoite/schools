<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Schools Bolivia</title>
	<!-- Aquí puedes agregar tus CSS, Tailwind, Bootstrap, etc. -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
	<!-- CDN de FontAwesome para íconos -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="{{ asset('css/corporativo.css') }}">
	@stack('styles')
</head>
<body class="bg-gray-100 min-h-screen">
	<div class="container mx-auto py-6">
		@yield('content')
	</div>
</body>
@stack('scripts')
</html>