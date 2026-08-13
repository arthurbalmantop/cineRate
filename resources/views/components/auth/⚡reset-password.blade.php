<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Component;

new class extends Component
{
    public string $token = '';
    public string $email = '';

    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function resetPassword()
    {
        $validated = $this->validate([
            'token' => ['required'],

            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8)
                    ->mixedCase()
                    ->numbers(),
            ],
        ]);

        $status = Password::reset(
            [
                'email' => $validated['email'],
                'password' => $validated['password'],
                'password_confirmation' => $this->password_confirmation,
                'token' => $validated['token'],
            ],

            function (User $user, string $password) {

                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {

            session()->flash(
                'status',
                'Senha redefinida com sucesso. Faça login com sua nova senha.'
            );

            return $this->redirectRoute('login');
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
                Redefinir senha
            </h1>

            <p class="mt-2 text-sm text-zinc-400">
                Escolha uma nova senha para sua conta.
            </p>

        </div>


        <form wire:submit="resetPassword" class="space-y-5">

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
                    readonly
                    class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-3 text-zinc-400">

                @error('email')
                <span class="mt-1 block text-sm text-red-400">
                    {{ $message }}
                </span>
                @enderror

            </div>


            <div x-data="{ showPassword: false }">

                <label
                    for="password"
                    class="mb-1 block text-sm font-medium text-zinc-200">
                    Nova senha
                </label>

                <div class="flex items-center rounded-lg border border-zinc-700 bg-zinc-950 px-3">

                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        wire:model="password"
                        autocomplete="new-password"
                        class="w-full bg-transparent py-3 text-white focus:outline-none">

                    <button
                        type="button"
                        @click="showPassword = ! showPassword"
                        class="ml-3 text-zinc-400 transition hover:text-white">
                        <span x-show="! showPassword">
                            👁
                        </span>

                        <span x-show="showPassword" x-cloak>
                            🙈
                        </span>
                    </button>

                </div>

                <p class="mt-1 text-xs text-zinc-500">
                    Mínimo de 8 caracteres, com maiúscula, minúscula e número.
                </p>

                @error('password')
                <span class="mt-1 block text-sm text-red-400">
                    {{ $message }}
                </span>
                @enderror

            </div>


            <div>

                <label
                    for="password_confirmation"
                    class="mb-1 block text-sm font-medium text-zinc-200">
                    Confirmar nova senha
                </label>

                <input
                    id="password_confirmation"
                    type="password"
                    wire:model="password_confirmation"
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-3 text-white focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400">

            </div>


            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="resetPassword"
                class="w-full rounded-lg bg-amber-400 px-4 py-3 font-bold text-black transition hover:bg-amber-300 disabled:opacity-60">

                <span wire:loading.remove wire:target="resetPassword">
                    Redefinir senha
                </span>

                <span wire:loading wire:target="resetPassword">
                    Redefinindo...
                </span>

            </button>

        </form>

    </div>

</div>