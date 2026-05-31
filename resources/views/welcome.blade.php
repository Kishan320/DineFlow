<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>RestaurantMS — Restaurant Management System</title>
        <meta name="description" content="Complete restaurant management system for order taking, table management, sales tracking, and reporting.">

        <!-- Plus Jakarta Sans font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- DataTables CSS (no Bootstrap theme) -->
        <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
    </head>
    <body>
        <div id="app"></div>
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <!-- DataTables -->
        <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    </body>
</html>
