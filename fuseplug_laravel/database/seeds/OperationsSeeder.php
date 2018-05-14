<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OperationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $brand = DB::table('brands')->get()[0];
        DB::table('operations')->insert([
            'brand_id' => $brand->id,
            'name' => 'credit_check_laravel',
            'queue' => 'fuseplug_laravel'
        ]);
        $operation = DB::table('operations')->get()[0];

        DB::table('operation_rules')->insert([
            'operation_id' => $operation->id,
            'brand_version' => 'v3',
            'fuse_version' => '57',
            'order' => 1,
            'acting_on' => 'request',
            'do_always' => false,
            'input_selector' => 'abc',
            'operator' => '=',
            'allowed_value' => 'special'
        ]);
        $operation_rule = DB::table('operation_rules')->get()[0];


        DB::table('operation_actions')->insert([
            'operation_rule_id' => $operation_rule->id,
            'order' => 1,
            'name' => 'send_stuff_to_whatever',
            'input' => 'jarjar',
            'operation_type' => 'http',
            'operation_source' => 'fuse',
            'source_data' => '',
            'brand_url' => 'http://dontcare.com/see/if/i/care',
            'fuse_url' => 'http://whatever.com/zero/worries',
            'http_verb' => 'GET'
        ]);
        $operation_action = DB::table('operation_actions')->get()[0];

        DB::table('operations')->insert([
            'brand_id' => $brand->id,
            'name' => 'credit_check_python',
            'queue' => 'fuseplug_python'
        ]);
        $operation = DB::table('operations')->get()[0];

//print_r($brand);
//print_r($operation);
//print_r($operation_rule);
//print_r($operation_action);

        
    }
}
