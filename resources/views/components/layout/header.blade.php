@props([
'backUrl' => null,
'backLabel' => 'Voltar',
])

<header class="border-b border-zinc-800 bg-zinc-950">

    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6">

        <!-- Logo  -->
        <a
            href="{{ route('feed') }}"
            class="flex items-center text-3xl font-bold">
            <span class="text-white">CINE</span>
            <span class="text-amber-400">RATE</span>
        </a>


        @auth

        <div class="flex items-center gap-4">

            <!-- Botão voltar -->
            @if ($backUrl)

            <a
                href="{{ $backUrl }}"
                class="inline-flex items-center gap-2 rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm font-medium text-zinc-300 transition hover:border-amber-400 hover:text-amber-400">
                
                <span class="hidden sm:inline">
                    {{ $backLabel }}
                </span>
            </a>

            @endif


            <!-- Usuário -->
            <a
                href="{{ route('profile.edit') }}"
                class="flex items-center gap-3">

                @if (auth()->user()->avatar_path)

                <img
                    src="{{ asset('storage/' . auth()->user()->avatar_path) }}"
                    alt="{{ auth()->user()->name }}"
                    class="h-9 w-9 rounded-full object-cover">

                @else

                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-zinc-800 text-sm font-bold text-amber-400">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                @endif


                <div class="hidden sm:block">

                    <p class="text-sm font-medium text-white">
                        {{ auth()->user()->name }}
                    </p>

                    @if (auth()->user()->username)
                    <p class="text-xs text-zinc-500">
                        {{ '@' . auth()->user()->username }}
                    </p>
                    @endif

                </div>

            </a>


            <!-- Logout -->
            <form
                method="POST"
                action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="rounded-lg border border-zinc-700 px-4 py-2 text-sm font-medium text-zinc-300 transition hover:border-red-500 hover:text-red-400">
                    Sair
                </button>

            </form>

        </div>

        @endauth

    </div>

</header>