<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Relationship;
use App\Models\RelationshipMember;
use App\Models\Post;
use App\Models\Milestone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with a premium relationship starter kit.
     */
    public function run(): void
    {
        // 1. Create the Couple
        $user1 = User::factory()->create([
            'name' => 'alex',
            'email' => 'alex@example.com',
            'password' => Hash::make('password'),
        ]);

        $user2 = User::factory()->create([
            'name' => 'jordan',
            'email' => 'jordan@example.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Create their Shared Space
        $relationship = Relationship::create([
            'name' => 'our happy place',
            'creator_id' => $user1->id,
            'anniversary_date' => now()->subYears(2)->subMonths(3)->subDays(15),
            'theme' => 'rose',
        ]);

        // 3. Link them together
        RelationshipMember::create([
            'relationship_id' => $relationship->id,
            'user_id' => $user1->id,
            'joined_at' => now()->subYears(2),
        ]);

        RelationshipMember::create([
            'relationship_id' => $relationship->id,
            'user_id' => $user2->id,
            'joined_at' => now()->subYears(2),
        ]);

        // 4. Create some Milestones
        Milestone::create([
            'relationship_id' => $relationship->id,
            'title' => 'our first meeting',
            'event_date' => now()->subYears(2)->subMonths(4),
            'description' => 'at that small coffee shop on the corner.',
            'category' => 'first',
        ]);

        Milestone::create([
            'relationship_id' => $relationship->id,
            'title' => 'our first trip to bali',
            'event_date' => now()->subYears(1)->subMonths(6),
            'description' => 'sunsets, beaches, and pure magic.',
            'category' => 'travel',
        ]);

        Milestone::create([
            'relationship_id' => $relationship->id,
            'title' => 'upcoming anniversary',
            'event_date' => now()->addMonths(2),
            'description' => 'planning something big!',
            'category' => 'anniversary',
        ]);

        // 5. Create some Journal Entries
        Post::create([
            'relationship_id' => $relationship->id,
            'user_id' => $user1->id,
            'type' => 'journal',
            'title' => 'thinking of you',
            'content' => "just wanted to write down how grateful i am for our journey. every day feels like a new page in a beautiful book we're writing together.",
            'mood' => '🥰',
            'published_at' => now()->subDays(5),
        ]);

        Post::create([
            'relationship_id' => $relationship->id,
            'user_id' => $user2->id,
            'type' => 'journal',
            'title' => 'a rainy afternoon',
            'content' => "even on the quietest, rainiest days, your presence makes everything feel warm and bright. i love our simple moments.",
            'mood' => '🌙',
            'published_at' => now()->subDays(2),
        ]);

        // 6. Create a "This Day" memory from last year
        Post::create([
            'relationship_id' => $relationship->id,
            'user_id' => $user1->id,
            'type' => 'note',
            'content' => "exactly one year ago we decided to start this gallery. look how far we've come!",
            'mood' => '✨',
            'published_at' => now()->subYears(1),
        ]);
    }
}
