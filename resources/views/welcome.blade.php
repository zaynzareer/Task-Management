<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#FDFDFC] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <header class="w-full mb-6 not-has-[nav]:hidden">
            @if (Route::has('login'))
                <nav class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto">
                    <h2 class="text-xl font-bold text-orange-500">TaskFlow</h2>

                    <div class="flex gap-4 items-center">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="text-gray-600 hover:text-orange-500"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="text-gray-600 hover:text-orange-500"
                        >
                            Login
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition"
                            >
                                Sign Up
                            </a>
                        @endif
                    @endauth
                    </div>
                </nav>
            @endif
        </header>

        <section class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-orange-100 flex flex-col">
            <div class="max-w-7xl mx-auto px-6 py-16 flex flex-col md:flex-row items-center gap-12">
                <div class="flex-1">
                    <h1 class="text-5xl font-bold text-gray-800 leading-tight">
                        Organize Your Work.
                        <span class="text-orange-500">Achieve More.</span>
                    </h1>

                    <p class="mt-6 text-lg text-gray-600">
                        A simple and powerful task manager to help you stay focused,
                        track progress, and get things done faster.
                    </p>

                    @if (Route::has('register') || Route::has('login'))
                        <div class="mt-8 flex gap-4">
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-6 py-3 bg-orange-500 text-white rounded-xl shadow hover:bg-orange-600 transition">
                                    Sign Up
                                </a>
                            @endif
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="px-6 py-3 border border-orange-500 text-orange-500 rounded-xl hover:bg-orange-50 transition">
                                    Log In
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex-1">
                    <img
                        src="{{ asset('storage/landing_page_image.png') }}"
                        alt="Task management dashboard"
                        class="w-40% max-w-lg mx-auto"
                    />
                </div>
            </div>

            <div class="max-w-6xl mx-auto px-6 pb-16">
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-orange-100">
                        <h3 class="text-xl font-semibold text-gray-800">Smart Task Lists</h3>
                        <p class="mt-2 text-gray-600">
                            Organize tasks into projects and lists so nothing gets lost.
                        </p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-orange-100">
                        <h3 class="text-xl font-semibold text-gray-800">Progress Tracking</h3>
                        <p class="mt-2 text-gray-600">
                            Monitor your productivity and track completed tasks easily.
                        </p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-orange-100">
                        <h3 class="text-xl font-semibold text-gray-800">Focus Mode</h3>
                        <p class="mt-2 text-gray-600">
                            Stay distraction-free with a clean workspace for your tasks.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>
