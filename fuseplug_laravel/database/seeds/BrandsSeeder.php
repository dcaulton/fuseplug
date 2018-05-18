<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('brands')->insert([
            'name' => 'test_brand'
        ]);
        DB::table('brands')->insert([
            'name' => 'mock_brand'
        ]);
    }
}
