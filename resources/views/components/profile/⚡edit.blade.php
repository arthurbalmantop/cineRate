<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $username = '';
    public string $bio = '';
    public string $birth_date = '';
    public string $gender = '';
    public string $city = '';
    public string $country = '';

    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public $avatar;

    public function mount(): void
    {
        $user = User::findOrFail(Auth::id());

        $this->name = $user->name;
        $this->username = $user->username ?? '';
        $this->bio = $user->bio ?? '';
        $this->birth_date = $user->birth_date?->format('Y-m-d') ?? '';
        $this->gender = $user->gender ?? '';
        $this->city = $user->city ?? '';
        $this->country = $user->country ?? '';
    }

    public function save(): void
    {
        $user = User::findOrFail(Auth::id());

        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($user->id),
            ],

            'bio' => [
                'nullable',
                'string',
                'max:500',
            ],

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'birth_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'gender' => [
                'nullable',
                Rule::in([
                    'male',
                    'female',
                    'prefer_not_to_say',
                ]),
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'country' => [
                'nullable',
                'string',
                'max:100',
            ],
        ], [
            'username.regex' => 'O nome de usuário pode conter apenas letras, números, ponto, hífen e underline.',
            'avatar.image' => 'O arquivo escolhido deve ser uma imagem.',
            'avatar.mimes' => 'A foto deve estar nos formatos JPG, JPEG, PNG ou WEBP.',
            'avatar.max' => 'A foto deve ter no máximo 2 MB.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'bio' => $validated['bio'],
            'birth_date' => $validated['birth_date'],
            'gender' => $validated['gender'],
            'city' => $validated['city'],
            'country' => $validated['country'],
        ]);

        if ($this->avatar) {
            $oldAvatar = $user->avatar_path;

            $path = $this->avatar->store(
                path: 'avatars',
                options: 'public'
            );

            $user->update([
                'avatar_path' => $path,
            ]);

            if ($oldAvatar) {
                Storage::disk('public')->delete($oldAvatar);
            }

            $this->reset('avatar');
        }

        session()->flash(
            'success',
            'Perfil atualizado com sucesso.'
        );
    }

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => [
                'required',
                'current_password',
            ],

            'new_password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers(),
            ],
        ], [
            'current_password.required' => 'Informe sua senha atual.',
            'current_password.current_password' => 'A senha atual está incorreta.',
            'new_password.required' => 'Informe a nova senha.',
        ]);

        $user = User::findOrFail(Auth::id());

        $user->password = $validated['new_password'];
        $user->save();

        $this->reset([
            'current_password',
            'new_password',
            'new_password_confirmation',
        ]);

        session()->flash(
            'password-success',
            'Senha alterada com sucesso.'
        );
    }
};
?>

<div class="min-h-screen bg-black text-white">

    <x-layout.header
        :back-url="route('feed')"
        back-label="Voltar ao feed" />

    <main class="mx-auto max-w-2xl px-4 py-10 sm:px-6">

        <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-6 shadow-xl">

            <div class="mb-6">
                <h1 class="text-2xl font-bold text-white">
                    Meu perfil
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Atualize suas informações pessoais no cineRate.
                </p>
            </div>

            @if (session('success'))
            <div class="mb-6 rounded-lg border border-emerald-800 bg-emerald-950 px-4 py-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
            @endif

            <form wire:submit="save" class="space-y-5">

                <div>
                    <label class="mb-3 block text-sm font-medium text-zinc-200">
                        Foto de perfil
                    </label>

                    <div class="flex items-center gap-5">

                        <div class="h-24 w-24 overflow-hidden rounded-full bg-zinc-800">

                            @if ($this->avatar)

                            <img
                                src="{{ $avatar->temporaryUrl() }}"
                                alt="Prévia da foto"
                                class="h-full w-full object-cover">

                            @elseif (auth()->user()->avatar_path)

                            <img
                                src="{{ asset('storage/' . auth()->user()->avatar_path) }}"
                                alt="Foto de perfil"
                                class="h-full w-full object-cover">

                            @else

                            <div class="flex h-full w-full items-center justify-center text-2xl font-bold text-amber-400">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>

                            @endif

                        </div>

                        <div>
                            <label
                                for="avatar"
                                class="cursor-pointer rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-2 text-sm font-medium text-zinc-200 hover:border-amber-400 hover:text-amber-400">
                                Escolher foto
                            </label>

                            <input
                                id="avatar"
                                type="file"
                                wire:model="avatar"
                                accept="image/jpeg,image/png,image/webp"
                                class="hidden">

                            <p class="mt-2 text-xs text-zinc-400">
                                JPG, PNG ou WEBP. Máximo de 2 MB.
                            </p>

                            @error('avatar')
                            <p class="mt-1 text-sm text-red-400">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                    </div>
                </div>

                <div>
                    <label
                        for="name"
                        class="mb-1 block text-sm font-medium text-zinc-200">
                        Nome
                    </label>

                    <input
                        id="name"
                        type="text"
                        wire:model="name"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-white placeholder:text-zinc-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400 focus:border-gray-500 focus:outline-none">

                    @error('name')
                    <p class="mt-1 text-sm text-red-400">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="username"
                        class="mb-1 block text-sm font-medium text-zinc-200">
                        Nome de usuário
                    </label>

                    <div class="flex rounded-lg border border-zinc-700 bg-zinc-950 focus-within:border-amber-400 focus-within:ring-1 focus-within:ring-amber-400">
                        <span class="flex items-center px-3 text-zinc-500">
                            @
                        </span>

                        <input
                            id="username"
                            type="text"
                            wire:model="username"
                            class="w-full rounded-r-lg bg-transparent px-2 py-2 text-white placeholder:text-zinc-500 focus:outline-none"
                            placeholder="seunome">
                    </div>

                    <p class="mt-1 text-xs text-zinc-400">
                        Use letras, números, ponto, hífen ou underline.
                    </p>

                    @error('username')
                    <p class="mt-1 text-sm text-red-400">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="email"
                        class="mb-1 block text-sm font-medium text-zinc-200">
                        E-mail
                    </label>

                    <input
                        id="email"
                        type="email"
                        value="{{ auth()->user()->email }}"
                        disabled
                        class="w-full cursor-not-allowed rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-zinc-400">

                    <p class="mt-1 text-xs text-zinc-400">
                        O e-mail não pode ser alterado por esta tela.
                    </p>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">

                    <div>
                        <label
                            for="birth_date"
                            class="mb-1 block text-sm font-medium text-zinc-200">
                            Data de nascimento
                        </label>

                        <input
                            id="birth_date"
                            type="date"
                            wire:model="birth_date"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-white placeholder:text-zinc-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400">

                        @error('birth_date')
                        <p class="mt-1 text-sm text-red-400">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>


                    <div>
                        <label
                            for="gender"
                            class="mb-1 block text-sm font-medium text-zinc-200">
                            Gênero
                        </label>

                        <select
                            id="gender"
                            wire:model="gender"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-white placeholder:text-zinc-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400">
                            <option value="">Selecione</option>
                            <option value="female">Feminino</option>
                            <option value="male">Masculino</option>
                            <option value="prefer_not_to_say">Prefiro não informar</option>
                        </select>

                        @error('gender')
                        <p class="mt-1 text-sm text-red-400">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>


                    <div>
                        <label
                            for="city"
                            class="mb-1 block text-sm font-medium text-zinc-200">
                            Cidade
                        </label>

                        <input
                            id="city"
                            type="text"
                            wire:model="city"
                            placeholder="Ex.: São Paulo"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-white placeholder:text-zinc-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400">

                        @error('city')
                        <p class="mt-1 text-sm text-red-400">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>


                    <div>
                        <label
                            for="country"
                            class="mb-1 block text-sm font-medium text-zinc-200">
                            País
                        </label>

                        <input
                            id="country"
                            type="text"
                            wire:model="country"
                            placeholder="Ex.: Brasil"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-white placeholder:text-zinc-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400">

                        @error('country')
                        <p class="mt-1 text-sm text-red-400">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                </div>

                <div>
                    <div class="mb-1 flex items-center justify-between">

                        <label
                            for="bio"
                            class="text-sm font-medium text-zinc-200">
                            Bio
                        </label>

                        <span class="text-xs text-zinc-500">
                            {{ strlen($bio) }}/500
                        </span>

                    </div>

                    <textarea
                        id="bio"
                        wire:model.live="bio"
                        rows="5"
                        maxlength="500"
                        class="w-full resize-none rounded-lg border border-gray-300 px-3 py-2 focus:border-gray-500 focus:outline-none"
                        placeholder="Conte um pouco sobre você e o que gosta de assistir..."></textarea>

                    @error('bio')
                    <p class="mt-1 text-sm text-red-400">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="flex justify-end border-t border-zinc-800 pt-5">

                    <button
                        type="submit"
                        class="rounded-lg bg-amber-400 px-5 py-2.5 text-sm font-bold text-black transition hover:bg-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-300">
                        Salvar alterações
                    </button>

                </div>

            </form>

        </div>

        <div class="mt-8 rounded-xl border border-zinc-800 bg-zinc-950 p-6">

            <div class="mb-6">
                <h2 class="text-xl font-bold text-white">
                    Alterar senha
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Para sua segurança, informe a senha atual antes de definir uma nova.
                </p>
            </div>

            @if (session('password-success'))
            <div class="mb-5 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('password-success') }}
            </div>
            @endif

            <form wire:submit="updatePassword" class="space-y-5">

                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-200">
                        Senha atual
                    </label>

                    <input
                        type="password"
                        wire:model="current_password"
                        autocomplete="current-password"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-white placeholder:text-zinc-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400">

                    @error('current_password')
                    <p class="mt-1 text-sm text-red-400">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-200">
                        Nova senha
                    </label>

                    <input
                        type="password"
                        wire:model="new_password"
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-white placeholder:text-zinc-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400">

                    <p class="mt-1 text-xs text-zinc-400">
                        Mínimo de 8 caracteres, com letra maiúscula,
                        minúscula e número.
                    </p>

                    @error('new_password')
                    <p class="mt-1 text-sm text-red-400">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-zinc-200">
                        Confirmar nova senha
                    </label>

                    <input
                        type="password"
                        wire:model="new_password_confirmation"
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2 text-white placeholder:text-zinc-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400">
                </div>

                <div class="flex justify-end border-t border-zinc-800 pt-5">
                    <button
                        type="submit"
                        class="rounded-lg bg-amber-400 px-5 py-2.5 text-sm font-bold text-black transition hover:bg-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-300">
                        Alterar senha
                    </button>
                </div>

            </form>

        </div>

    </main>

    <x-layout.footer />

</div>