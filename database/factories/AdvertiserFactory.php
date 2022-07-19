<?php

use App\Model\Advertiser;
use App\Model\region;
use App\Model\ServiceCategory;
use Faker\Generator as Faker;

$factory->define(Advertiser::class, function (Faker $faker) {
    $category = ServiceCategory::first();
    $region = region::first();
    return [
        'name' => $faker->name,
        'category_id' => $category->id,
        'region_id' => $region->id,
    ];
});
