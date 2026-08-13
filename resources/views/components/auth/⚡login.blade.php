<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public string $email = '';
    public string $password = '';

    public function login()
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            $this->addError('email', 'E-mail ou senha inválidos.');

            return;
        }

        request()->session()->regenerate();

        return $this->redirect('/feed');
    }
};
?>

<div class="login-background relative min-h-screen overflow-hidden text-white">

    <!-- CONTEÚDO -->
    <div class="relative z-10 flex min-h-screen items-center justify-center px-4 py-10">

        <div class="w-full max-w-xl">

            <!-- LOGO / TOPO -->
            <div class="mb-8 text-center">

                <!-- <div class="mb-5 flex justify-center">
                    <div class="relative flex h-28 w-28 items-center justify-center rounded-full border-4 border-amber-400 bg-zinc-950/70 shadow-[0_0_40px_rgba(251,191,36,0.15)]">
                        <div class="grid grid-cols-2 gap-3">
                            <span class="h-5 w-5 rounded-full bg-amber-300"></span>
                            <span class="h-5 w-5 rounded-full bg-amber-300"></span>
                            <span class="h-5 w-5 rounded-full bg-amber-300"></span>
                            <span class="h-5 w-5 rounded-full bg-amber-300"></span>
                        </div>

                        <div class="absolute -bottom-1 left-2 h-6 w-6 rotate-45 border-b-4 border-l-4 border-amber-400 bg-zinc-950/70"></div>

                        <div class="absolute -bottom-2 -right-2 flex h-12 w-12 items-center justify-center rounded-full bg-red-500 shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6 text-white">
                                <path fill-rule="evenodd" d="M10.788 3.212a.75.75 0 0 1 1.424 0l2.082 5.26 5.63.414a.75.75 0 0 1 .424 1.316l-4.32 3.76 1.35 5.49a.75.75 0 0 1-1.11.826L12 17.771l-4.918 2.507a.75.75 0 0 1-1.11-.826l1.35-5.49-4.32-3.76a.75.75 0 0 1 .424-1.316l5.63-.414 2.082-5.26Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div> -->

                <h1 class="text-5xl font-extrabold tracking-wide sm:text-7xl">
                    <span class="text-white">CINE</span>
                    <span class="text-amber-400"> RATE</span>
                </h1>

            </div>


            <!-- CARD LOGIN -->
            <div class="mx-auto w-full max-w-md rounded-[24px] border border-white/15 bg-zinc-900/50 p-5 shadow-2xl backdrop-blur-md sm:p-7">

                <div class="mb-8 text-center">
                    <h2 class="text-2xl font-bold text-white">
                        Bem-vindo de volta!
                    </h2>

                    <p class="mt-2 text-base text-zinc-300">
                        Faça login para continuar
                    </p>
                </div>


                <form wire:submit="login" class="space-y-5">

                    <!-- E-MAIL -->
                    <div>
                        <label class="sr-only">E-mail</label>

                        <div class="flex items-center rounded-2xl border border-white/20 bg-white/5 px-4 py-4 shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="mr-4 h-7 w-7 text-zinc-300">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 7.5v9A2.25 2.25 0 0 1 19.5 18.75h-15A2.25 2.25 0 0 1 2.25 16.5v-9m19.5 0A2.25 2.25 0 0 0 19.5 5.25h-15A2.25 2.25 0 0 0 2.25 7.5m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.918l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 9.66A2.25 2.25 0 0 1 2.25 7.743V7.5" />
                            </svg>

                            <input
                                type="email"
                                wire:model="email"
                                autocomplete="email"
                                placeholder="E-mail"
                                class="w-full bg-transparent text-base text-white placeholder:text-zinc-400 focus:outline-none">
                        </div>

                        @error('email')
                        <span class="mt-2 block text-sm text-red-400">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>


                    <!-- SENHA -->
                    <div>
                        <label class="sr-only">Senha</label>

                        <div
                            x-data="{ showPassword: false }"
                            class="flex items-center rounded-2xl border border-white/20 bg-white/5 px-4 py-4 shadow-inner">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.8"
                                stroke="currentColor"
                                class="mr-4 h-7 w-7 text-zinc-300">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5A2.25 2.25 0 0 1 19.5 12.75v6A2.25 2.25 0 0 1 17.25 21h-10.5A2.25 2.25 0 0 1 4.5 18.75v-6A2.25 2.25 0 0 1 6.75 10.5Z" />
                            </svg>

                            <input
                                :type="showPassword ? 'text' : 'password'"
                                wire:model="password"
                                autocomplete="current-password"
                                placeholder="Senha"
                                class="w-full bg-transparent text-base text-white placeholder:text-zinc-400 focus:outline-none">

                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="ml-3 text-zinc-300 transition hover:text-white"
                                :aria-label="showPassword ? 'Ocultar senha' : 'Mostrar senha'">

                                {{-- olho aberto --}}
                                <svg
                                    x-show="!showPassword"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.8"
                                    stroke="currentColor"
                                    class="h-7 w-7">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>


                                {{-- olho fechado --}}
                                <svg
                                    x-show="showPassword"
                                    x-cloak
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.8"
                                    stroke="currentColor"
                                    class="h-7 w-7">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 0 0 2.036 11.68a1.017 1.017 0 0 0 0 .639C3.423 16.49 7.36 19.5 12 19.5c1.67 0 3.238-.39 4.636-1.08M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639a10.52 10.52 0 0 1-2.293 3.95M6.228 6.228 3 3m3.228 3.228 4.955 4.955m8.487 5.087L21 21m-4.73-4.73-4.955-4.955" />
                                </svg>

                            </button>
                        </div>

                        @error('password')
                        <span class="mt-2 block text-sm text-red-400">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>


                    <!-- BOTÃO ENTRAR -->
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-amber-400 px-4 py-3 text-base font-extrabold uppercase tracking-wide text-black transition hover:bg-amber-300">
                        Entrar
                    </button>

                </form>


                <!-- DIVISOR -->
                <div class="my-8 flex items-center gap-4">
                    <div class="h-px flex-1 bg-white/15"></div>
                    <span class="text-2xl text-zinc-300">ou</span>
                    <div class="h-px flex-1 bg-white/15"></div>
                </div>


                <!-- CRIAR CONTA -->
                <a
                    href="/register"
                    class="flex w-full items-center justify-center gap-3 rounded-2xl border border-amber-400 px-4 py-5 text-2xl font-semibold text-amber-400 transition hover:bg-amber-400/10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-8 w-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>

                    CRIAR MINHA CONTA
                </a>

            </div>


            <!-- TEXTO SEGURANÇA -->
            <div class="mt-8 text-center">
                <p class="flex items-center justify-center gap-2 text-xl text-zinc-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5A2.25 2.25 0 0 1 19.5 12.75v6A2.25 2.25 0 0 1 17.25 21h-10.5A2.25 2.25 0 0 1 4.5 18.75v-6A2.25 2.25 0 0 1 6.75 10.5Z" />
                    </svg>
                    Seus dados estão protegidos
                </p>

                <a
                    href="{{ route('password.request') }}"
                    class="mt-10 inline-block text-2xl text-amber-400 underline decoration-dotted underline-offset-8 hover:text-amber-300">
                    Esqueceu sua senha?
                </a>
            </div>

        </div>

    </div>

</div>