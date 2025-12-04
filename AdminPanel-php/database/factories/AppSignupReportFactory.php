<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\AppSignupReport;
use Faker\Generator as Faker;

$factory->define(AppSignupReport::class, function (Faker $faker) {

    return [
        'user_id' => $faker->randomDigitNotNull,
        'email' => $faker->word,
        'status' => $faker->word,
        'signup_date' => $faker->word,
        'signedin_date' => $faker->word,
        'subscription' => $faker->word,
        'emails_sent' => $faker->randomDigitNotNull,
        'emails_problems' => $faker->randomDigitNotNull,
        'device' => $faker->word,
        'Country' => $faker->word,
        'OS' => $faker->word,
        'last_seen' => $faker->date('Y-m-d H:i:s'),
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
