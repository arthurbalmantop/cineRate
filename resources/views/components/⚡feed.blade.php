<?php

use App\Models\Follow;
use App\Models\Post;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public int $perPage = 5;

    public function logout()
    {

        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return $this->redirect('/login');
    }

    public function posts()
    {

        return Post::query()
            ->with('user')
            ->withCount([
                'votes as recommend_count' => function ($query) {
                    $query->where('type', 'recommend');
                },

                'votes as not_recommend_count' => function ($query) {
                    $query->where('type', 'not_recommend');
                },

                'follows as followers_count',
            ])
            ->with([
                'votes' => function ($query) {
                    $query->where('user_id', Auth::id());
                },

                'follows' => function ($query) {
                    $query->where('user_id', Auth::id());
                },
            ])
            ->latest()
            ->paginate($this->perPage);
    }

    public function vote(int $postId, string $type)
    {

        if (! in_array($type, ['recommend', 'not_recommend'], true)) {
            return;
        }

        $post = Post::findOrFail($postId);

        if ($post->status !== 'open') {
            return;
        }

        DB::transaction(function () use ($post, $type) {

            Vote::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'post_id' => $post->id,
                ],
                [
                    'type' => $type,
                ]
            );

            Follow::firstOrCreate([
                'user_id' => Auth::id(),
                'post_id' => $post->id,
            ]);
        });
    }

    public function toggleFollow(int $postId)
    {

        $post = Post::findOrFail($postId);

        if ($post->status !== 'open') {
            return;
        }

        $follow = Follow::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->first();

        if ($follow) {
            $follow->delete();

            return;
        }

        Follow::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
        ]);
    }

    public function closePost(int $postId)
    {

        $post = Post::findOrFail($postId);

        Gate::authorize('close', $post);

        $post->update([
            'status' => 'closed',
        ]);
    }

    public function deletePost(int $postId)
    {

        $post = Post::findOrFail($postId);

        Gate::authorize('delete', $post);

        $post->delete();
    }

    public function myPosts()
    {
        return Post::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->limit(5)
            ->get();
    }

    public function followedPosts()
    {
        return Follow::query()
            ->where('user_id', Auth::id())
            ->with('post.user')
            ->latest()
            ->limit(3)
            ->get();
    }

    public function updatedPerPage(): void
    {
        $allowed = [5, 10, 20, 50];

        if (! in_array($this->perPage, $allowed, true)) {
            $this->perPage = 5;
        }

        $this->resetPage();
    }
};
?>

@php
/** @var \App\Models\User $currentUser */
$currentUser = auth()->user();
@endphp

<div class="min-h-screen bg-black text-white">

    <x-layout.header />

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6">

        <div class="grid items-start gap-6 lg:grid-cols-[280px_minmax(0,1fr)_280px]">

            <aside class="space-y-6 lg:sticky lg:top-6">
                <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg">

                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="font-bold text-white">
                            Minhas publicações
                        </h2>
                        <span class="text-xs text-zinc-500">
                            Recentes
                        </span>
                    </div>

                    <div class="space-y-3">
                        @forelse ($this->myPosts() as $myPost)

                        <div class="rounded-lg border border-zinc-800 bg-zinc-950 p-3">
                            <p class="truncate text-sm font-semibold text-white">
                                {{ $myPost->title }}
                            </p>

                            <div class="mt-2 flex items-center justify-between gap-2">
                                <span class="text-xs text-zinc-400">
                                    {{ $myPost->type === 'movie' ? 'Filme' : 'Série' }}
                                </span>

                                <span
                                    class="text-xs font-medium
                                            {{ $myPost->status === 'open'
                                                ? 'text-emerald-400'
                                                : 'text-zinc-500' }}">
                                    {{ $myPost->status === 'open' ? 'Aberta' : 'Encerrada' }}
                                </span>
                            </div>

                            <p class="mt-2 text-xs text-zinc-500">
                                {{ $myPost->created_at->format('d/m/Y') }}
                            </p>
                        </div>

                        @empty

                        <div class="rounded-lg border border-zinc-800 bg-zinc-950 p-4 text-center">
                            <p class="text-sm text-zinc-400">
                                Você ainda não publicou nada.
                            </p>
                        </div>

                        @endforelse
                        <a
                            href="{{ route('posts.mine') }}"
                            class="mt-4 flex w-full items-center justify-center rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-2 text-sm font-medium text-zinc-200 transition hover:border-amber-400 hover:text-amber-400">
                            Ver todas minhas publicações
                        </a>
                    </div>

                </div>

                <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg">

                    <div class="mb-4">
                        <h2 class="font-bold text-white">
                            Acompanhando
                        </h2>

                        <p class="mt-1 text-xs text-zinc-500">
                            Publicações que você acompanha
                        </p>
                    </div>

                    <div class="space-y-3">

                        @forelse ($this->followedPosts() as $follow)

                        @php
                        $followedPost = $follow->post;
                        @endphp

                        <div class="rounded-lg border border-zinc-800 bg-zinc-950 p-3">

                            <p class="truncate text-sm font-semibold text-white">
                                {{ $followedPost->title }}
                            </p>

                            <p class="mt-1 truncate text-xs text-zinc-400">
                                por {{ $followedPost->user->name }}
                            </p>

                            <div class="mt-2 flex items-center justify-between gap-2">

                                <span class="text-xs text-zinc-500">
                                    {{ $followedPost->type === 'movie' ? 'Filme' : 'Série' }}
                                </span>

                                <span
                                    class="text-xs font-medium
                            {{ $followedPost->status === 'open'
                                ? 'text-emerald-400'
                                : 'text-zinc-500' }}">
                                    {{ $followedPost->status === 'open'
                            ? 'Aberta'
                            : 'Encerrada' }}
                                </span>

                            </div>

                        </div>

                        @empty

                        <div class="rounded-lg border border-zinc-800 bg-zinc-950 p-4 text-center">

                            <p class="text-sm text-zinc-400">
                                Você ainda não acompanha nenhuma publicação.
                            </p>

                        </div>

                        @endforelse

                    </div>

                    <a
                        href="{{ route('following.index') }}"
                        class="mt-4 flex w-full items-center justify-center rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-2 text-sm font-medium text-zinc-200 transition hover:border-amber-400 hover:text-amber-400">
                        Ver todas
                    </a>

                </div>
            </aside>

            <section class="min-w-0">

                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white">
                            Publicações
                        </h1>

                        <p class="mt-1 text-sm text-zinc-400">
                            Veja o que a comunidade está assistindo e recomendando.
                        </p>
                    </div>

                    <div class="flex items-center gap-2">

                        <label
                            for="perPage"
                            class="text-sm text-zinc-400">
                            Exibir
                        </label>

                        <select
                            id="perPage"
                            wire:model.live="perPage"
                            class="rounded-lg border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm text-white outline-none transition focus:border-amber-400 focus:ring-1 focus:ring-amber-400">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>

                        <span class="text-sm text-zinc-400">
                            por página
                        </span>

                    </div>
                </div>


                <div id="feed-posts" class="space-y-5">

                    @forelse ($this->posts() as $post)

                    @php
                    $myVote = $post->votes->first()?->type;
                    $isFollowing = $post->follows->isNotEmpty();
                    @endphp

                    <article class="rounded-xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg sm:p-6">

                        <div class="flex items-start justify-between gap-4">

                            <div class="flex min-w-0 items-center gap-3">

                                <div class="h-11 w-11 shrink-0 overflow-hidden rounded-full border border-zinc-700 bg-zinc-800">
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

                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-white">
                                        {{ $post->user->name }}
                                    </p>

                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-zinc-500">
                                        @if ($post->user->username)
                                        <span>{{ '@' . $post->user->username }}</span>
                                        @endif
                                        <span>{{ $post->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>

                            </div>

                            <span
                                class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold
                                        {{ $post->status === 'open'
                                            ? 'bg-emerald-950 text-emerald-300'
                                            : 'bg-zinc-800 text-zinc-400' }}">
                                {{ $post->status === 'open' ? 'Aberta' : 'Encerrada' }}
                            </span>

                        </div>

                        <div class="mt-5">

                            <span class="rounded-full bg-amber-400/10 px-2.5 py-1 text-xs font-semibold text-amber-400">
                                {{ $post->type === 'movie' ? 'Filme' : 'Série' }}
                            </span>

                            <h2 class="mt-3 text-xl font-bold text-white">
                                {{ $post->title }}
                            </h2>

                            <p class="mt-3 whitespace-pre-line leading-relaxed text-zinc-300">
                                {{ $post->description }}
                            </p>

                        </div>

                        <div class="mt-6 flex flex-wrap items-center gap-3">

                            <button
                                type="button"
                                wire:click="vote({{ $post->id }}, 'recommend')"
                                @disabled($post->status !== 'open')
                                class="rounded-lg border px-4 py-2 text-sm font-medium transition
                                {{ $myVote === 'recommend'
                                            ? 'border-emerald-500 bg-emerald-500 text-black'
                                            : 'border-zinc-700 bg-zinc-950 text-zinc-200 hover:border-emerald-500 hover:text-emerald-400' }}
                                disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                👍 Recomendo
                                <span class="ml-1">{{ $post->recommend_count }}</span>
                            </button>

                            <button
                                type="button"
                                wire:click="vote({{ $post->id }}, 'not_recommend')"
                                @disabled($post->status !== 'open')
                                class="rounded-lg border px-4 py-2 text-sm font-medium transition
                                {{ $myVote === 'not_recommend'
                                            ? 'border-red-500 bg-red-500 text-white'
                                            : 'border-zinc-700 bg-zinc-950 text-zinc-200 hover:border-red-500 hover:text-red-400' }}
                                disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                👎 Não recomendo
                                <span class="ml-1">{{ $post->not_recommend_count }}</span>
                            </button>

                            <button
                                type="button"
                                wire:click="toggleFollow({{ $post->id }})"
                                @disabled($post->status !== 'open')
                                class="rounded-lg border px-4 py-2 text-sm font-medium transition
                                {{ $isFollowing
                                            ? 'border-amber-400 bg-amber-400 text-black'
                                            : 'border-zinc-700 bg-zinc-950 text-zinc-200 hover:border-amber-400 hover:text-amber-400' }}
                                disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                👀 {{ $isFollowing ? 'Acompanhando' : 'Acompanhar' }}
                                <span class="ml-1">{{ $post->followers_count }}</span>
                            </button>

                        </div>

                        @if ($currentUser->id === $post->user_id)

                        <div class="mt-5 flex flex-wrap gap-3 border-t border-zinc-800 pt-5">

                            @if ($post->status === 'open')
                            <button
                                type="button"
                                wire:click="closePost({{ $post->id }})"
                                class="rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-2 text-sm font-medium text-zinc-200 transition hover:border-zinc-500 hover:bg-zinc-700">
                                Encerrar publicação
                            </button>
                            @endif

                            @if (
                            ($post->recommend_count + $post->not_recommend_count) === 0
                            && $post->followers_count === 0
                            )

                            <div x-data="{ confirmDelete: false }">

                                <button
                                    type="button"
                                    @click="confirmDelete = true"
                                    class="rounded-lg border border-red-800 bg-red-950 px-4 py-2 text-sm font-medium text-red-300 transition hover:border-red-600 hover:bg-red-900">
                                    Excluir
                                </button>

                                <div
                                    x-show="confirmDelete"
                                    x-cloak
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 px-4">
                                    <div
                                        @click.outside="confirmDelete = false"
                                        class="w-full max-w-md rounded-xl border border-zinc-700 bg-zinc-900 p-6 shadow-2xl">
                                        <h2 class="text-lg font-bold text-white">
                                            Excluir publicação?
                                        </h2>

                                        <p class="mt-2 text-sm text-zinc-400">
                                            Essa ação não poderá ser desfeita.
                                        </p>

                                        <div class="mt-6 flex justify-end gap-3">

                                            <button
                                                type="button"
                                                @click="confirmDelete = false"
                                                class="rounded-lg border border-zinc-700 bg-zinc-800 px-4 py-2 text-sm font-medium text-zinc-200 hover:bg-zinc-700">
                                                Cancelar
                                            </button>

                                            <button
                                                type="button"
                                                @click="
                                                                confirmDelete = false;
                                                                $wire.deletePost({{ $post->id }});
                                                            "
                                                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">
                                                Sim, excluir
                                            </button>

                                        </div>
                                    </div>
                                </div>

                            </div>

                            @endif

                        </div>

                        @endif

                    </article>

                    @empty

                    <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-8 text-center shadow-lg">
                        <h2 class="text-lg font-bold text-white">
                            Ainda não há publicações.
                        </h2>

                        <p class="mt-2 text-sm text-zinc-400">
                            Seja a primeira pessoa a compartilhar uma indicação.
                        </p>
                    </div>

                    @endforelse

                    <div class="mt-8">
                        {{ $this->posts()->links(data: ['scrollTo' => '#feed-posts']) }}
                    </div>

                </div>

            </section>

            <aside class="lg:sticky lg:top-6">

                <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-5 shadow-lg">

                    <h2 class="font-bold text-white">
                        Compartilhe uma indicação
                    </h2>

                    <p class="mt-2 text-sm leading-relaxed text-zinc-400">
                        Assistiu algo interessante? Conte para a comunidade se vale a pena.
                    </p>

                    <a
                        href="{{ route('posts.create') }}"
                        class="mt-5 flex w-full items-center justify-center gap-2 rounded-lg bg-amber-400 px-4 py-2.5 text-sm font-bold text-black transition hover:bg-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-300">
                        <span class="text-lg leading-none">+</span>
                        Nova publicação
                    </a>

                </div>

            </aside>

        </div>

    </main>

    <x-layout.footer />

</div>