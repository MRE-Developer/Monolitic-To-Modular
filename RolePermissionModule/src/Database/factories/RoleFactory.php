<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(RoleModule\Role::class, function (Faker $faker) {
    return [
        'name' => Str::random(5),
        'display_name' => implode(" ", $faker->words(2)),
        'description' => $faker->sentence,
        'removable' => true,
    ];
});
