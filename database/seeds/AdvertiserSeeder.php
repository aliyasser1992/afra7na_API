<?php

use App\Model\Advertiser;
use Illuminate\Database\Seeder;

class AdvertiserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Advertiser::class, 500)->create();
    }
}
