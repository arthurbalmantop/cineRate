<?php

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    #[Computed]
    public function posts()
    {
        return Post::query()
            ->where('user_id', Auth::id())
            ->withCount([
                'votes as recommend_count' => function ($query) {
                    $query->where('type', 'recommend');
                },

                'votes as not_recommend_count' => function ($query) {
                    $query->where('type', 'not_recommend');
                },

                'follows as followers_count',
            ])
            ->latest()
            ->paginate(10);
    }
};

?>

<div class="min-h-screen bg-black text-white">

    <x-layout.header
        :back-url="route('feed')"
        back-label="Voltar ao feed" />


    <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6">

        <div class="mb-6">

            <h1 class="text-2xl font-bold text-white">
                Minhas publicações
            </h1>

            <p class="mt-1 text-sm text-zinc-400">
                Veja todas as publicações que você criou.
            </p>

        </div>


        <div id="my-posts" class="space-y-5">

            @forelse ($this->posts as $post)

            <article class="rounded-xl border border-zinc-800 bg-zinc-900 p-6 shadow-lg">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                    <div class="min-w-0">

                        <div class="mb-2 flex flex-wrap items-center gap-2">

                            <span class="rounded-full bg-zinc-800 px-2.5 py-1 text-xs font-medium text-zinc-300">
                                {{ $post->type === 'movie' ? 'Filme' : 'Série' }}
                            </span>

                            <span
                                class="rounded-full px-2.5 py-1 text-xs font-medium
                                        {{ $post->status === 'open'
                                            ? 'bg-emerald-950 text-emerald-400'
                                            : 'bg-zinc-800 text-zinc-400' }}">
                                {{ $post->status === 'open' ? 'Aberta' : 'Encerrada' }}
                            </span>

                        </div>

                        <h2 class="text-xl font-bold text-white">
                            {{ $post->title }}
                        </h2>

                        <p class="mt-1 text-xs text-zinc-500">
                            Publicado em
                            {{ $post->created_at->format('d/m/Y \à\s H:i') }}
                        </p>

                    </div>

                </div>


                <p class="mt-4 whitespace-pre-line text-sm leading-6 text-zinc-300">
                    {{ $post->description }}
                </p>


                <div class="mt-5 flex flex-wrap gap-4 border-t border-zinc-800 pt-4 text-sm">

                    <span class="text-emerald-400">
                        {{ $post->recommend_count }}
                        {{ $post->recommend_count === 1 ? 'recomendação' : 'recomendações' }}
                    </span>

                    <span class="text-red-400">
                        {{ $post->not_recommend_count }}
                        não recomendam
                    </span>

                    <span class="text-zinc-400">
                        {{ $post->followers_count }}
                        acompanhando
                    </span>

                </div>

            </article>

            @empty

            <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-10 text-center">

                <h2 class="text-lg font-bold text-white">
                    Você ainda não publicou nada.
                </h2>

                <p class="mt-2 text-sm text-zinc-400">
                    Crie sua primeira publicação no CineRate.
                </p>

                <a
                    href="{{ route('posts.create') }}"
                    class="mt-5 inline-flex rounded-lg bg-amber-400 px-5 py-2.5 text-sm font-bold text-black transition hover:bg-amber-300">
                    Nova publicação
                </a>

            </div>

            @endforelse

        </div>


        <div class="mt-8">
            {{ $this->posts->links(data: ['scrollTo' => '#my-posts']) }}
        </div>

    </main>

    <x-layout.footer />

</div>