<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register()
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers(),
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        request()->session()->regenerate();

        return $this->redirect('/feed');
    }
};
?>

<div class="min-h-screen bg-black px-4 py-10 text-white">

    <div class="mx-auto flex min-h-[calc(100vh-5rem)] max-w-md items-center justify-center">

        <div class="w-full rounded-2xl border border-zinc-800 bg-zinc-900 p-6 shadow-2xl sm:p-8">

            <div class="mb-6 text-center">

                <h1 class="text-2xl font-bold text-white">
                    Criar conta
                </h1>

                <p class="mt-2 text-sm text-zinc-400">
                    Crie sua conta para participar do CineRate.
                </p>

            </div>

            <form wire:submit="register" class="space-y-5">

                {{-- Nome --}}
                <div>
                    <label
                        for="name"
                        class="mb-1 block text-sm font-medium text-zinc-200"
                    >
                        Nome
                    </label>

                    <input
                        id="name"
                        type="text"
                        wire:model="name"
                        autocomplete="name"
                        placeholder="Seu nome"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-white placeholder:text-zinc-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"
                    >

                    @error('name')
                        <span class="mt-1 block text-sm text-red-400">
                            {{ $message }}
                        </span>
                    @enderror
                </div>


                {{-- E-mail --}}
                <div>
                    <label
                        for="email"
                        class="mb-1 block text-sm font-medium text-zinc-200"
                    >
                        E-mail
                    </label>

                    <input
                        id="email"
                        type="email"
                        wire:model="email"
                        autocomplete="email"
                        placeholder="seu@email.com"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-white placeholder:text-zinc-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"
                    >

                    @error('email')
                        <span class="mt-1 block text-sm text-red-400">
                            {{ $message }}
                        </span>
                    @enderror
                </div>


                {{-- Senha --}}
                <div>
                    <label
                        for="password"
                        class="mb-1 block text-sm font-medium text-zinc-200"
                    >
                        Senha
                    </label>

                    <input
                        id="password"
                        type="password"
                        wire:model="password"
                        autocomplete="new-password"
                        placeholder="Digite sua senha"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-white placeholder:text-zinc-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"
                    >

                    <p class="mt-1 text-xs text-zinc-500">
                        Use pelo menos 8 caracteres, incluindo letra maiúscula,
                        letra minúscula e número.
                    </p>

                    @error('password')
                        <span class="mt-1 block text-sm text-red-400">
                            {{ $message }}
                        </span>
                    @enderror
                </div>


                {{-- Confirmar senha --}}
                <div>
                    <label
                        for="password_confirmation"
                        class="mb-1 block text-sm font-medium text-zinc-200"
                    >
                        Confirmar senha
                    </label>

                    <input
                        id="password_confirmation"
                        type="password"
                        wire:model="password_confirmation"
                        autocomplete="new-password"
                        placeholder="Digite novamente sua senha"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-white placeholder:text-zinc-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"
                    >
                </div>


                {{-- Criar conta --}}
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="register"
                    class="w-full rounded-lg bg-amber-400 px-4 py-3 font-bold text-black transition hover:bg-amber-300 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="register">
                        Criar conta
                    </span>

                    <span wire:loading wire:target="register">
                        Criando conta...
                    </span>
                </button>

            </form>


            <div class="my-6 flex items-center gap-3">

                <div class="h-px flex-1 bg-zinc-800"></div>

                <span class="text-xs text-zinc-500">
                    ou
                </span>

                <div class="h-px flex-1 bg-zinc-800"></div>

            </div>


            <p class="text-center text-sm text-zinc-400">
                Já possui uma conta?

                <a
                    href="{{ route('login') }}"
                    class="font-medium text-amber-400 transition hover:text-amber-300"
                >
                    Entrar
                </a>
            </p>

        </div>

    </div>

</div>