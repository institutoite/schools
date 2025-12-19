<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Schools Bolivia</title>
	<!-- Aquí puedes agregar tus CSS, Tailwind, Bootstrap, etc. -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
	<!-- CDN de FontAwesome para íconos -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKB4Imkb9hAQZ8a3Q8h+5Q5e0siJ5u1T9e+Vd5p5Q5e0siJ5u1T9e+Vd5p5Q5e0siJ5u1T9e+Vd5p5Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gray-100 min-h-screen">
	<div class="container mx-auto py-6">
		@yield('content')
	</div>
</body>
</html>