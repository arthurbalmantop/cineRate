<?php

use Illuminate\Support\Facades\Password;
use Livewire\Component;

new class extends Component
{
    public string $email = '';

    public function sendResetLink(): void
    {
        $validated = $this->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink([
            'email' => $validated['email'],
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash(
                'status',
                'Enviamos um link de redefinição para o seu e-mail.'
            );

            return;
        }

        $this->addError(
            'email',
            __($status)
        );
    }
};

?>

<div class="login-background flex min-h-screen items-center justify-center px-4 py-10 text-white">

    <div class="w-full max-w-md rounded-2xl border border-zinc-800 bg-zinc-900/80 p-6 shadow-2xl backdrop-blur-md sm:p-8">

        <div class="mb-6 text-center">

            <a
                href="{{ route('login') }}"
                class="inline-flex text-3xl font-bold">
                <span class="text-white">CINE</span>
                <span class="text-amber-400">RATE</span>
            </a>

            <h1 class="mt-6 text-2xl font-bold text-white">
                Esqueceu sua senha?
            </h1>

            <p class="mt-2 text-sm text-zinc-400">
                Informe seu e-mail e enviaremos um link para criar uma nova senha.
            </p>

        </div>


        @if (session('status'))

        <div class="mb-5 rounded-lg border border-emerald-900 bg-emerald-950/50 p-3 text-sm text-emerald-400">
            {{ session('status') }}
        </div>

        @endif


        <form wire:submit="sendResetLink" class="space-y-5">

            <div>

                <label
                    for="email"
                    class="mb-1 block text-sm font-medium text-zinc-200">
                    E-mail
                </label>

                <input
                    id="email"
                    type="email"
                    wire:model="email"
                    autocomplete="email"
                    placeholder="seu@email.com"
                    class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-3 text-white placeholder:text-zinc-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400">

                @error('email')
                <span class="mt-1 block text-sm text-red-400">
                    {{ $message }}
                </span>
                @enderror

            </div>


            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="sendResetLink"
                class="w-full rounded-lg bg-amber-400 px-4 py-3 font-bold text-black transition hover:bg-amber-300 disabled:opacity-60">

                <span wire:loading.remove wire:target="sendResetLink">
                    Enviar link
                </span>

                <span wire:loading wire:target="sendResetLink">
                    Enviando...
                </span>

            </button>

        </form>


        <div class="mt-6 text-center">

            <a
                href="{{ route('login') }}"
                class="text-sm font-medium text-zinc-400 transition hover:text-amber-400">
                Voltar para o login
            </a>

        </div>

    </div>

</div>