@extends('layouts.app')
@section('content')

<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">

    <div class="w-full max-w-md bg-white border border-gray-200 rounded-lg shadow-sm">

        {{-- HEADER --}}
        <div class="px-8 pt-8 pb-4 text-center">
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ trans('panel.site_title') }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ trans('global.register') }}
            </p>
        </div>

        {{-- FORM --}}
        <form method="POST" action="{{ route('register') }}" class="px-8 pb-8 space-y-5">
            @csrf

            {{-- NAME --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ trans('global.user_name') }}
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       autofocus
                       class="w-full px-3 py-2 border rounded-md text-sm
                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                              {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }}">
                @if($errors->has('name'))
                    <p class="mt-1 text-xs text-red-600">
                        {{ $errors->first('name') }}
                    </p>
                @endif
            </div>

            {{-- EMAIL --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ trans('global.login_email') }}
                </label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       class="w-full px-3 py-2 border rounded-md text-sm
                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                              {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }}">
                @if($errors->has('email'))
                    <p class="mt-1 text-xs text-red-600">
                        {{ $errors->first('email') }}
                    </p>
                @endif
            </div>

            {{-- PASSWORD --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ trans('global.login_password') }}
                </label>
                <input type="password"
                       name="password"
                       required
                       class="w-full px-3 py-2 border rounded-md text-sm
                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                              {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }}">
                @if($errors->has('password'))
                    <p class="mt-1 text-xs text-red-600">
                        {{ $errors->first('password') }}
                    </p>
                @endif
            </div>

            {{-- CONFIRM PASSWORD --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ trans('global.login_password_confirmation') }}
                </label>
                <input type="password"
                       name="password_confirmation"
                       required
                       class="w-full px-3 py-2 border rounded-md text-sm
                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                              border-gray-300">
            </div>

            {{-- ACTION --}}
            <div class="pt-2">
                <button type="submit"
                        class="w-full py-2.5 bg-blue-600 text-white text-sm font-medium
                               rounded-md hover:bg-blue-700 transition">
                    {{ trans('global.register') }}
                </button>
            </div>

            {{-- LOGIN LINK --}}
            <div class="text-center pt-2">
                <a href="{{ route('login') }}"
                   class="text-sm text-blue-600 hover:underline">
                    Already have an account? Login
                </a>
            </div>

        </form>
    </div>
</div>

@endsection
