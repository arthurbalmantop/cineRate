<?php

use App\Models\Follow;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    #[Computed]
    public function followedPosts()
    {
        return Follow::query()
            ->where('user_id', Auth::id())
            ->with([
                'post' => function ($query) {
                    $query
                        ->with('user')
                        ->withCount([
                            'votes as recommend_count' => function ($query) {
                                $query->where('type', 'recommend');
                            },

                            'votes as not_recommend_count' => function ($query) {
                                $query->where('type', 'not_recommend');
                            },

                            'follows as followers_count',
                        ]);
                },
            ])
            ->latest()
            ->paginate(10);
    }
};
?>

@php
/** @var \App\Models\User $currentUser */
$currentUser = auth()->user();
@endphp

<div class="min-h-screen bg-black text-white">

    <x-layout.header
        :back-url="route('feed')"
        back-label="Voltar ao feed" />


    <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6">

        <div class="mb-8">

            <h1 class="text-2xl font-bold text-white">
                Publicações que acompanho
            </h1>

            <p class="mt-1 text-sm text-zinc-400">
                Veja todas as publicações que você está acompanhando.
            </p>

        </div>


        <div class="space-y-5">

            @forelse ($this->followedPosts as $follow)

            @php
            $post = $follow->post;
            @endphp

            <article
                class="rounded-xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg">

                <div class="flex items-start justify-between gap-4">

                    <div class="flex items-center gap-3">

                        <div class="h-10 w-10 overflow-hidden rounded-full border border-zinc-700 bg-zinc-800">

                            @if ($post->user->avatar_path)

                            <img
                                src="{{ asset('storage/' . $post->user->avatar_path) }}"
                                alt="Foto de {{ $post->user->name }}"
                                class="h-full w-full object-cover">

                            @else

                            <div class="flex h-full w-full items-center justify-center font-bold text-amber-400">
                                {{ strtoupper(substr($post->user->name, 0, 1)) }}
                            </div>

                            @endif

                        </div>

                        <div>

                            <p class="text-sm font-semibold text-white">
                                {{ $post->user->name }}
                            </p>

                            @if ($post->user->username)

                            <p class="text-xs text-zinc-500">
                                {{ '@' . $post->user->username }}
                            </p>

                            @endif

                        </div>

                    </div>


                    <span
                        class="rounded-full px-2.5 py-1 text-xs font-semibold
                                {{ $post->status === 'open'
                                    ? 'bg-emerald-950 text-emerald-300'
                                    : 'bg-zinc-800 text-zinc-400' }}">
                        {{ $post->status === 'open'
                                ? 'Aberta'
                                : 'Encerrada' }}
                    </span>

                </div>


                <div class="mt-5">

                    <span class="text-xs font-semibold text-amber-400">
                        {{ $post->type === 'movie'
                                ? 'Filme'
                                : 'Série' }}
                    </span>

                    <h2 class="mt-2 text-xl font-bold text-white">
                        {{ $post->title }}
                    </h2>

                    <p class="mt-3 leading-relaxed text-zinc-300">
                        {{ $post->description }}
                    </p>

                </div>

                <div class="mt-5 flex flex-wrap items-center gap-3 border-t border-zinc-800 pt-4">

                    {{-- Recomendações --}}
                    <div class="flex items-center gap-2 rounded-lg bg-emerald-950/50 px-3 py-2">
                        <span class="text-sm font-semibold text-emerald-400">
                            {{ $post->recommend_count }}
                        </span>

                        <span class="text-xs text-emerald-300">
                            {{ $post->recommend_count === 1 ? 'recomenda' : 'recomendam' }}
                        </span>
                    </div>


                    {{-- Não recomendações --}}
                    <div class="flex items-center gap-2 rounded-lg bg-red-950/50 px-3 py-2">
                        <span class="text-sm font-semibold text-red-400">
                            {{ $post->not_recommend_count }}
                        </span>

                        <span class="text-xs text-red-300">
                            não recomendam
                        </span>
                    </div>


                    {{-- Acompanhando --}}
                    <div class="flex items-center gap-2 rounded-lg bg-zinc-800 px-3 py-2">
                        <span class="text-sm font-semibold text-zinc-200">
                            {{ $post->followers_count }}
                        </span>

                        <span class="text-xs text-zinc-400">
                            acompanhando
                        </span>
                    </div>

                </div>


                <div class="mt-5 border-t border-zinc-800 pt-4">

                    <p class="text-xs text-zinc-500">
                        Acompanhando desde
                        {{ $follow->created_at->format('d/m/Y H:i') }}
                    </p>

                </div>

            </article>

            @empty

            <div
                class="rounded-xl border border-zinc-800 bg-zinc-900 p-8 text-center">

                <h2 class="font-bold text-white">
                    Nenhuma publicação acompanhada
                </h2>

                <p class="mt-2 text-sm text-zinc-400">
                    Quando você acompanhar ou votar em uma publicação,
                    ela aparecerá aqui.
                </p>

                <a
                    href="{{ route('feed') }}"
                    class="mt-5 inline-flex rounded-lg bg-amber-400 px-5 py-2.5 text-sm font-bold text-black transition hover:bg-amber-300">
                    Explorar publicações
                </a>

            </div>

            @endforelse

        </div>


        @if ($this->followedPosts->hasPages())

        <div class="mt-8">
            {{ $this->followedPosts->links() }}
        </div>

        @endif

    </main>

    <x-layout.footer />

</div>