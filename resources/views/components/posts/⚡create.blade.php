<?php

use App\Models\Post;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public string $title = '';
    public string $type = '';
    public string $description = '';

    public function save()
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => [
                'required',
                Rule::in(['movie', 'series']),
            ],
            'description' => [
                'required',
                'string',
                'max:2000',
            ],
        ], [
            'title.required' => 'Informe o título.',
            'type.required' => 'Escolha filme ou série.',
            'type.in' => 'Tipo inválido.',
            'description.required' => 'Escreva seu comentário.',
        ]);

        Post::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'type' => $validated['type'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);

        return $this->redirect('/feed');
    }
};
?>



<div class="min-h-screen bg-black text-white">

    <x-layout.header
        :back-url="route('feed')"
        back-label="Voltar ao feed" />


    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6">

        <div class="mx-auto max-w-2xl">

            <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-6 shadow-xl sm:p-8">

                <div class="mb-6">

                    <h1 class="text-2xl font-bold text-white">
                        Nova publicação
                    </h1>

                    <p class="mt-1 text-sm text-zinc-400">
                        Compartilhe sua opinião sobre um filme ou série.
                    </p>

                </div>


                <form wire:submit="save" class="space-y-5">

                    <!-- Título  -->
                    <div>

                        <label
                            for="title"
                            class="mb-1 block text-sm font-medium text-zinc-200">
                            Título
                        </label>

                        <input
                            id="title"
                            type="text"
                            wire:model="title"
                            placeholder="Ex.: Breaking Bad"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-white placeholder:text-zinc-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400">

                        @error('title')
                        <p class="mt-1 text-sm text-red-400">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    <!-- Tipo  -->
                    <div>

                        <label
                            for="type"
                            class="mb-1 block text-sm font-medium text-zinc-200">
                            Tipo
                        </label>

                        <select
                            id="type"
                            wire:model="type"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-white focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400">
                            <option value="">Selecione</option>
                            <option value="movie">Filme</option>
                            <option value="series">Série</option>
                        </select>

                        @error('type')
                        <p class="mt-1 text-sm text-red-400">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    <!-- Comentário  -->
                    <div>

                        <div class="mb-1 flex items-center justify-between">

                            <label
                                for="description"
                                class="text-sm font-medium text-zinc-200">
                                Comentário
                            </label>

                            <span class="text-xs text-zinc-500">
                                Máximo de 2000 caracteres
                            </span>

                        </div>

                        <textarea
                            id="description"
                            wire:model="description"
                            rows="6"
                            maxlength="2000"
                            placeholder="Conte o que você achou ou por que recomenda essa obra..."
                            class="w-full resize-none rounded-lg border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-white placeholder:text-zinc-500 focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400"></textarea>

                        @error('description')
                        <p class="mt-1 text-sm text-red-400">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    <!-- Ações  -->
                    <div class="flex flex-col-reverse gap-3 border-t border-zinc-800 pt-5 sm:flex-row sm:justify-end">

                        <a
                            href="{{ route('feed') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-zinc-700 bg-zinc-800 px-5 py-2.5 text-sm font-medium text-zinc-200 transition hover:border-zinc-600 hover:bg-zinc-700">
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-amber-400 px-5 py-2.5 text-sm font-bold text-black transition hover:bg-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-300 disabled:cursor-not-allowed disabled:opacity-60"
                            wire:loading.attr="disabled"
                            wire:target="save">
                            <span wire:loading.remove wire:target="save">
                                Publicar
                            </span>

                            <span wire:loading wire:target="save">
                                Publicando...
                            </span>
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </main>

    <x-layout.footer />

</div>