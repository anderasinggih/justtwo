<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        $themeColors = [
            'light' => '#ffffff',
            'dark' => '#000000',
            'rose' => '#fff1f2',
            'midnight' => '#0f172a',
        ];
        $bgColor = $themeColors[$theme ?? 'light'] ?? '#ffffff';
    @endphp
    
    <meta name="theme-color" content="{{ $bgColor }}">
    <title>{{ config('app.name', 'GalleryTwo') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased" style="background-color: {{ $bgColor }}">
    {{ $slot }}
</body>
</html>
