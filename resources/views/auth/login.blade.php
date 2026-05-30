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
                {{ trans('global.login') }}
            </p>
        </div>

        {{-- INFO MESSAGE --}}
        @if(session('message'))
            <div class="mx-8 mb-4 px-4 py-2 text-sm rounded-md
                        bg-blue-50 text-blue-700 border border-blue-200">
                {{ session('message') }}
            </div>
        @endif

        {{-- FORM --}}
        <form method="POST" action="{{ route('login') }}" class="px-8 pb-8 space-y-5">
            @csrf

            {{-- EMAIL --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ trans('global.login_email') }}
                </label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autofocus
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

            {{-- REMEMBER + FORGOT --}}
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox"
                           name="remember"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    {{ trans('global.remember_me') }}
                </label>

                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm text-blue-600 hover:underline">
                        {{ trans('global.forgot_password') }}
                    </a>
                @endif
            </div>

            {{-- LOGIN BUTTON --}}
            <div class="pt-2">
                <button type="submit"
                        class="w-full py-2.5 bg-blue-600 text-white text-sm font-medium
                               rounded-md hover:bg-blue-700 transition">
                    {{ trans('global.login') }}
                </button>
            </div>

            {{-- REGISTER LINK --}}
            <div class="text-center pt-2">
                <a href="{{ route('register') }}"
                   class="text-sm text-blue-600 hover:underline">
                    {{ trans('global.register') }}
                </a>
            </div>

        </form>
    </div>
</div>

@endsection
