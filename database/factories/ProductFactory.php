<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Product;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Faker\Provider\Base;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Product::class, function (Faker $faker) {
    
    return [
	    'code' => $faker->name,
	    'name' => $faker->name,
	    'barcode_symbology' => 'C39',
	    'price_net' => $faker->randomNumber(2),
	    'tax_id' => null,
	    'price_gross' => $faker->randomNumber(2),
	    'service' => $faker->numberBetween(0, 1),
	    'purchase_price_net' => $faker->randomNumber(2),
	    'purchase_tax_id' => null,
	    'purchase_price_gross' => $faker->randomNumber(2),
	    'alert_quantity' => 1,
	    'quantity' => $faker->randomNumber(2),
	    'created_by' => 1,
    ];

});
