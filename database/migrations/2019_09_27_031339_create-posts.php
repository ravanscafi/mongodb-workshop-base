<?php

use App\Author;
use App\Comment;
use App\Post;
use Mongolid\Laravel\Migrations\AbstractMigration;
use Faker\Generator as Faker;

class CreatePosts extends AbstractMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $faker = app(Faker::class);
        $authors = [];

        foreach (range(1, 10) as $i) {
            /** @var Author $author */
            $author = Author::fill([
                'name' => $faker->name,
                'avatar' => $faker->imageUrl(100, 100, 'people'),
            ]);

            $author->save();
            $authors[] = $author;
        }

        foreach (range(1, 9) as $j) {
            /** @var Post $post */
            $post = Post::fill([
                'title' => $faker->sentence,
                'body' => $faker->paragraph(20),
                'tags' => $faker->words(mt_rand(2, 5)),
                'cover' => $faker->imageUrl(400, 250, 'nature'),
            ]);

            $post->author()->attach($faker->randomElement($authors));

            foreach (range(1, mt_rand(2, 4)) as $k) {
                /** @var Comment $comment */
                $comment = Comment::fill([
                    'body' => $faker->paragraph(5),
                ]);

                $comment->author()->attach($faker->randomElement($authors));

                $post->comments()->add($comment);
            }

            $post->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $author = new Author();
        $author->getCollection()->drop();

        $post = new Post();
        $post->getCollection()->drop();
    }
}
