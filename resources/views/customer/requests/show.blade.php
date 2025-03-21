<x-customer-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Požadavek: ') }} {{ $request->name }}
            </h2>
            <div>
                <a href="{{ route('customer.requests.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Zpět na seznam') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Informace o požadavku -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informace o požadavku</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-semibold">Projektová položka:</p>
                            <p class="text-gray-700">
                                {{ $request->projectItem->name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Projekt:</p>
                            <p class="text-gray-700">
                                {{ $request->projectItem->project->name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Firma:</p>
                            <p class="text-gray-700">
                                {{ $request->projectItem->project->customer->name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Vytvořeno:</p>
                            <p class="text-gray-700">{{ $request->inserted->format('d.m.Y H:i') }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Stav:</p>
                            <p class="text-gray-700">
                                @if ($request->state == 1)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Nový</span>
                                @elseif ($request->state == 2)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">V
                                        řešení</span>
                                @elseif ($request->state == 3)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Čeká
                                        na zpětnou vazbu</span>
                                @elseif ($request->state == 4)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Vyřešeno</span>
                                @elseif ($request->state == 5)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Uzavřeno</span>
                                @else
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Neznámý</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">Typ:</p>
                            <p class="text-gray-700">
                                @if ($request->kind == 1)
                                    Standardní
                                @elseif ($request->kind == 2)
                                    Chyba
                                @elseif ($request->kind == 3)
                                    Prioritní
                                @else
                                    Neznámý
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Zprávy -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Zprávy</h3>

                    <div class="space-y-6">
                        @forelse ($request->messages as $message)
                            <div
                                class="border rounded-lg p-4 {{ $message->id_user ? 'bg-blue-50 border-blue-200' : 'bg-green-50 border-green-200' }}">
                                <div class="flex justify-between mb-2">
                                    <div>
                                        <span class="font-medium">
                                            @if ($message->id_user && $message->user)
                                                {{ $message->user->fname }} {{ $message->user->lname }} (zaměstnanec)
                                            @elseif ($message->id_custuser && $message->customerUser)
                                                {{ $message->customerUser->fname }}
                                                {{ $message->customerUser->lname }} (zákazník)
                                            @else
                                                Neznámý uživatel
                                            @endif
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $message->inserted->format('d.m.Y H:i') }}
                                    </div>
                                </div>
                                <div class="text-gray-700 whitespace-pre-wrap">{{ $message->message }}</div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center">Žádné zprávy k tomuto požadavku.</p>
                        @endforelse
                    </div>

                    <!-- Formulář pro přidání nové zprávy - pokud požadavek není uzavřen -->
                    @if ($request->state != 5)
                        {{-- 5 = Uzavřeno --}}
                        <div class="mt-8">
                            <h4 class="text-md font-medium text-gray-900 mb-2">Přidat odpověď</h4>
                            <form action="{{ route('customer.requests.add-message', ['id' => $request->id]) }}"
                                method="POST">
                                @csrf
                                <div>
                                    <textarea name="message" rows="4"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Zadejte vaši zprávu..." required></textarea>
                                    <x-input-error :messages="$errors->get('message')" class="mt-2" />
                                </div>
                                <div class="mt-4 flex justify-end">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                        {{ __('Odeslat zprávu') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="mt-8 p-4 bg-gray-100 rounded">
                            <p class="text-gray-700">Tento požadavek je uzavřen a nelze k němu přidat další zprávy.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Akce pro uzavření požadavku - zobrazí se pouze pokud je požadavek ve stavu "Vyřešeno" -->
            @if ($request->state == 4)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Potvrzení vyřešení</h3>
                        <p class="mb-4">Tento požadavek byl označen jako vyřešený. Pokud souhlasíte s řešením, můžete
                            ho uzavřít.</p>
                        <form action="{{ route('customer.requests.confirm-resolution', $request) }}" method="POST">
                            @csrf
                            <!-- Odstraníme @method('PATCH') a použijeme jen běžný POST -->
                            <div class="flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    {{ __('Potvrdit vyřešení a uzavřít') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-customer-layout>
