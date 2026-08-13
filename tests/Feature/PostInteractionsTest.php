<?php

namespace Tests\Feature;

use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PostInteractionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_change_vote_without_creating_duplicate(): void
    {
        $user = User::factory()->create();

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Interestelar',
            'type' => 'movie',
            'description' => 'Vale a pena assistir?',
            'status' => 'open',
        ]);

        $component = Livewire::actingAs($user)
            ->test('feed');

        $component->call('vote', $post->id, 'recommend');

        $this->assertDatabaseHas('votes', [
            'user_id' => $user->id,
            'post_id' => $post->id,
            'type' => 'recommend',
        ]);

        // Votar também deve fazer o usuário acompanhar o post.
        $this->assertDatabaseHas('follows', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        // Usuário muda de opinião.
        $component->call('vote', $post->id, 'not_recommend');

        // Continua existindo somente um voto.
        $this->assertDatabaseCount('votes', 1);

        $this->assertDatabaseHas('votes', [
            'user_id' => $user->id,
            'post_id' => $post->id,
            'type' => 'not_recommend',
        ]);

        // E continua seguindo somente uma vez.
        $this->assertDatabaseCount('follows', 1);
    }

    public function test_closed_post_does_not_accept_votes_or_follows(): void
    {
        $user = User::factory()->create();

        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Breaking Bad',
            'type' => 'series',
            'description' => 'Ainda vale a pena começar?',
            'status' => 'closed',
        ]);

        $component = Livewire::actingAs($user)
            ->test('feed');

        $component->call('vote', $post->id, 'recommend');

        $component->call('toggleFollow', $post->id);

        $this->assertDatabaseMissing('votes', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $this->assertDatabaseMissing('follows', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_post_with_interactions_cannot_be_deleted(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $post = Post::create([
            'user_id' => $owner->id,
            'title' => 'O Poderoso Chefão',
            'type' => 'movie',
            'description' => 'Quero começar a trilogia.',
            'status' => 'open',
        ]);

        Vote::create([
            'user_id' => $otherUser->id,
            'post_id' => $post->id,
            'type' => 'recommend',
        ]);

        Livewire::actingAs($owner)
            ->test('feed')
            ->call('deletePost', $post->id)
            ->assertForbidden();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
        ]);
    }
}