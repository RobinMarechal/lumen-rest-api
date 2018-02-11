<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'email' => $faker->email,
    ];
});


$factory->define(App\Post::class, function (Faker\Generator $faker, $args) {
    return [
        'content' => $faker->text,
        'user_id' => isset($args['user_id']) ?: factory(App\User::class)->create()->id
    ];
});

$factory->define(App\Comment::class, function (Faker\Generator $faker, $args) {
    return [
        'content' => $faker->text,
        'post_id' => isset($args['post_id']) ?: factory(App\Post::class)->create()->id,
        'user_id' => isset($args['user_id']) ?: factory(App\User::class)->create()->id
    ];
});



