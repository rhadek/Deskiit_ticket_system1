<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DeskIT - Systém pro správu projektů a požadavků</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 dark:bg-gray-900">
    <!-- Navigační lišta -->
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">DeskIT</span>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="#funkce" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600">
                            Funkce
                        </a>
                        <a href="#vyhody" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600">
                            Výhody
                        </a>
                        <a href="#kontakt" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600">
                            Kontakt
                        </a>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Dashboard
                            </a>
                        @else
                            @if (Route::has('customer.login'))
                                <a href="{{ route('customer.login') }}" class="px-3 py-2 rounded-md text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300">
                                    Zákaznický portál
                                </a>
                            @endif
                            <a href="{{ route('login') }}" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                                Přihlášení
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-3 py-2 rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Registrace
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>

                <!-- Mobilní menu -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button type="button" class="mobile-menu-button bg-white dark:bg-gray-800 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Otevřít hlavní menu</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobilní menu, ukazuje/skrývá se na základě stavu menu -->
        <div class="sm:hidden hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="#funkce" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600">
                    Funkce
                </a>
                <a href="#vyhody" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600">
                    Výhody
                </a>
                <a href="#kontakt" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600">
                    Kontakt
                </a>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-700">
                <div class="space-y-1">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 border-indigo-500 text-base font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/30">
                                Dashboard
                            </a>
                        @else
                            @if (Route::has('customer.login'))
                                <a href="{{ route('customer.login') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600">
                                    Zákaznický portál
                                </a>
                            @endif
                            <a href="{{ route('login') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600">
                                Přihlášení
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600">
                                    Registrace
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero sekce -->
    <section class="pt-24 pb-12 sm:pt-32 sm:pb-16 bg-gradient-to-b from-indigo-50 to-white dark:from-gray-800 dark:to-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left">
                    <h1>
                        <span class="block text-sm font-semibold uppercase tracking-wide text-indigo-600 dark:text-indigo-400">DeskIT</span>
                        <span class="mt-1 block text-4xl tracking-tight font-extrabold sm:text-5xl xl:text-6xl">
                            <span class="block text-gray-900 dark:text-white">Správa projektů a</span>
                            <span class="block text-indigo-600 dark:text-indigo-400">požadavků</span>
                        </span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 dark:text-gray-300 sm:mt-5 sm:text-xl lg:text-lg xl:text-xl">
                        Kompletní systém pro řízení projektů, komunikaci se zákazníky a evidenci požadavků.
                        Zjednodušte si správu projektů a zlepšete komunikaci se zákazníky.
                    </p>
                    <div class="mt-8 sm:max-w-lg sm:mx-auto sm:text-center lg:text-left lg:mx-0">
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow">
                                <a href="{{ Route::has('login') ? route('login') : '#' }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10">
                                    Začít používat
                                </a>
                            </div>
                            <div class="mt-3 sm:mt-0 sm:ml-3">
                                <a href="#funkce" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900 hover:bg-indigo-200 dark:hover:bg-indigo-800 md:py-4 md:text-lg md:px-10">
                                    Zjistit více
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-12 relative sm:max-w-lg sm:mx-auto lg:mt-0 lg:max-w-none lg:mx-0 lg:col-span-6 lg:flex lg:items-center">
                    <div class="relative mx-auto w-full rounded-lg shadow-lg lg:max-w-md">
                        <div class="relative block w-full bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
                            <img class="w-full" src="https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Ukázka pracovního prostředí">
                            <div class="absolute inset-0 bg-gray-800 opacity-15"></div>
                            <div class="absolute inset-0 flex items-center justify-center"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Funkce -->
    <section id="funkce" class="py-12 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-indigo-600 dark:text-indigo-400 font-semibold tracking-wide uppercase">Funkce</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Komplexní řešení pro vaše projekty
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 dark:text-gray-300 lg:mx-auto">
                    DeskIT nabízí všechny nástroje, které potřebujete pro efektivní řízení projektů a správu požadavků.
                </p>
            </div>

            <div class="mt-10">
                <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900 dark:text-white">Správa zákazníků a projektů</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500 dark:text-gray-300">
                            Přehledná evidence zákazníků, jejich projektů a projektových položek. Hierarchická struktura umožňuje snadnou organizaci a přehled.
                        </dd>
                    </div>

                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900 dark:text-white">Ticketovací systém</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500 dark:text-gray-300">
                            Kompletní systém pro vytváření a správu požadavků. Zákazníci mohou snadno vytvářet nové požadavky a sledovat jejich stav.
                        </dd>
                    </div>

                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                </svg>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900 dark:text-white">Komunikační platforma</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500 dark:text-gray-300">
                            Přímá komunikace mezi zákazníky a pracovníky v rámci každého požadavku. Vše na jednom místě, bez nutnosti používat email.
                        </dd>
                    </div>

                    <div class="relative">
                        <dt>
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900 dark:text-white">Evidence práce</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500 dark:text-gray-300">
                            Sledování času stráveného na jednotlivých požadavcích. Detailní reporty pro efektivní řízení týmu a fakturaci.
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <!-- Výhody -->
    <section id="vyhody" class="py-12 bg-indigo-50 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-indigo-600 dark:text-indigo-400 font-semibold tracking-wide uppercase">Výhody</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Proč používat DeskIT?
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 dark:text-gray-300 lg:mx-auto">
                    Objevte výhody, které vám přinese implementace našeho systému.
                </p>
            </div>

            <div class="mt-10">
                <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-3 md:gap-x-8 md:gap-y-10">
                    <div class="text-center">
                        <div class="flex items-center justify-center h-20 w-20 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-500 dark:text-indigo-400 mx-auto">
                            <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg leading-6 font-medium text-gray-900 dark:text-white">Zvýšení efektivity</h3>
                        <p class="mt-2 text-base text-gray-500 dark:text-gray-300">
                            Automatizace rutinních úkonů a přehledná evidence všech informací na jednom místě šetří čas a zdroje.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="flex items-center justify-center h-20 w-20 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-500 dark:text-indigo-400 mx-auto">
                            <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg leading-6 font-medium text-gray-900 dark:text-white">Lepší komunikace</h3>
                        <p class="mt-2 text-base text-gray-500 dark:text-gray-300">
                            Přímá komunikace v kontextu jednotlivých požadavků eliminuje nedorozumění a zlepšuje spolupráci.
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="flex items-center justify-center h-20 w-20 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-500 dark:text-indigo-400 mx-auto">
                            <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg leading-6 font-medium text-gray-900 dark:text-white">Přehledné reporty</h3>
                        <p class="mt-2 text-base text-gray-500 dark:text-gray-300">
                            Detailní přehled o čase stráveném na projektech a výkonnosti týmu pro lepší rozhodování.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA sekce -->
    <section class="py-12 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    <span class="block">Připraveni začít?</span>
                </h2>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 dark:text-gray-300 lg:mx-auto">
                    Vyzkoušejte DeskIT ještě dnes a zlepšete způsob, jakým řídíte své projekty a komunikujete se zákazníky.
                </p>
                <div class="mt-8 flex justify-center">
                    <div class="inline-flex rounded-md shadow">
                        <a href="{{ Route::has('login') ? route('login') : '#' }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Přihlásit se
                        </a>
                    </div>
                    <div class="ml-3 inline-flex">
                        <a href="{{ Route::has('register') ? route('register') : '#' }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-900 hover:bg-indigo-200 dark:hover:bg-indigo-800">
                            Registrovat se
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Patička -->
    <footer id="kontakt" class="bg-gray-800 dark:bg-gray-950">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
            <div class="xl:grid xl:grid-cols-3 xl:gap-8">
                <div class="space-y-8 xl:col-span-1">
                    <div class="flex items-center">
                        <span class="text-2xl font-bold text-white">DeskIT</span>
                    </div>
                    <p class="text-gray-400 text-base">
                        Komplexní systém pro správu projektů, požadavků a komunikaci se zákazníky.
                    </p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-gray-300">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.194 0 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                            </svg>
                        </a>

                        <a href="#" class="text-gray-400 hover:text-gray-300">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                        </a>

                        <a href="#" class="text-gray-400 hover:text-gray-300">
                            <span class="sr-only">GitHub</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="mt-12 grid grid-cols-2 gap-8 xl:mt-0 xl:col-span-2">
                    <div class="md:grid md:grid-cols-2 md:gap-8">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-300 tracking-wider uppercase">Řešení</h3>
                            <ul role="list" class="mt-4 space-y-4">
                                <li>
                                    <a href="#" class="text-base text-gray-400 hover:text-gray-300">
                                        Správa projektů
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-400 hover:text-gray-300">
                                        Správa požadavků
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-400 hover:text-gray-300">
                                        Komunikace
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-400 hover:text-gray-300">
                                        Evidence práce
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-12 md:mt-0">
                            <h3 class="text-sm font-semibold text-gray-300 tracking-wider uppercase">Podpora</h3>
                            <ul role="list" class="mt-4 space-y-4">
                                <li>
                                    <a href="#" class="text-base text-gray-400 hover:text-gray-300">
                                        Dokumentace
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-400 hover:text-gray-300">
                                        Průvodce
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-400 hover:text-gray-300">
                                        Často kladené otázky
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-400 hover:text-gray-300">
                                        Kontaktní formulář
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="md:grid md:grid-cols-2 md:gap-8">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-300 tracking-wider uppercase">Společnost</h3>
                            <ul role="list" class="mt-4 space-y-4">
                                <li>
                                    <a href="#" class="text-base text-gray-400 hover:text-gray-300">
                                        O nás
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-400 hover:text-gray-300">
                                        Blog
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-400 hover:text-gray-300">
                                        Partneři
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-12 md:mt-0">
                            <h3 class="text-sm font-semibold text-gray-300 tracking-wider uppercase">Kontakt</h3>
                            <ul role="list" class="mt-4 space-y-4">
                                <li class="flex">
                                    <svg class="flex-shrink-0 h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="ml-3 text-base text-gray-400">info@deskit.cz</span>
                                </li>
                                <li class="flex mt-4">
                                    <svg class="flex-shrink-0 h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span class="ml-3 text-base text-gray-400">+420 123 456 789</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-12 border-t border-gray-700 pt-8">
                <p class="text-base text-gray-400 xl:text-center">
                    &copy; 2025 DeskIT. Všechna práva vyhrazena.
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Jednoduchý script pro ovládání mobilního menu
        document.addEventListener('DOMContentLoaded', function() {
            // Mobilní menu
            const menuButton = document.querySelector('.mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            menuButton.addEventListener('click', function() {
                const expanded = menuButton.getAttribute('aria-expanded') === 'true';
                menuButton.setAttribute('aria-expanded', !expanded);
                mobileMenu.classList.toggle('hidden');

                // Změna ikon
                menuButton.querySelector('svg.block').classList.toggle('hidden');
                menuButton.querySelector('svg.hidden').classList.toggle('hidden');
            });
        });
    </script>
</body>
</html>
