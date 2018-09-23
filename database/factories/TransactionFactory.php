<?php
use App\Transaction;
use App\Seller;
use App\User;
use Faker\Generator as Faker;

$factory->define(Transaction::class, function (Faker $faker) {
    $vendedor = Seller::has('products')->get()->random();
    $comprador = User::all()->except($vendedor->id)->random();
    return [
        // 'name' => $faker->word,
        'quality' => $faker->numberBetween(1,3),
        'buyer_id' => $comprador->id,
        'product_id' =>$vendedor->products->random()->id,
    ];
});
