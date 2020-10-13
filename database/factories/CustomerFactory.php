<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Customer;
use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'phone' => $faker->phoneNumber,
        'company' => $faker->company,
        'vat_number' => $faker->bankAccountNumber,
        'gst_number' => $faker->bankAccountNumber,
        'address' => $faker->streetAddress,
        'city' => $faker->cityPrefix,
        'state' => $faker->state,
        'postal_code' => $faker->postcode,
        'country' => $faker->country,
    ];
});
