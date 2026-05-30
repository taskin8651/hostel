<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ trans('panel.site_title') }}</title>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Font Awesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    {{-- Select2 / Dropzone (agar login page me kabhi use ho) --}}
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css">

    @yield('styles')
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    {{-- LOGIN CARD --}}
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-6">

        {{-- LOGO / TITLE --}}
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                {{ trans('panel.site_title') }}
            </h1>
            <p class="text-sm text-gray-500">
                Login to continue
            </p>
        </div>

        {{-- CONTENT --}}
        @yield('content')

    </div>

    @yield('scripts')
</body>

</html>
