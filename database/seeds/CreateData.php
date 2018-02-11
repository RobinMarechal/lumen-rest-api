<?php

use Illuminate\Database\Seeder;

class CreateData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nbUsers = rand(3, 5);
        factory(App\User::class, $nbUsers)->create();

        $posts = [];
        $nbPosts = rand(3, 5);
        for($i = 0; $i < $nbPosts; $i++){
            $user_id = rand(1, $nbUsers);
            $created = factory(App\Post::class, rand(1, 3))->create(compact('user_id'));
            $posts = array_merge($posts, $created->toArray());
        }

        foreach ($posts as $post){
            $nbComments = rand(1, 5);

            for ($i = 0; $i < $nbComments; $i++){
                $user_id = rand(1, $nbUsers);
                factory(App\Comment::class)->create(['user_id' => $user_id, 'post_id' => $post['id']]);
            }
        }
    }
}
