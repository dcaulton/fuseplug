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
        $test_brand = DB::table('brands')->orderBy('id', 'asc')->first();
        $mock_brand = DB::table('brands')->orderBy('id', 'desc')->first();
        DB::table('operations')->insert([
            'brand_id' => $test_brand->id,
            'name' => 'credit_check_laravel',
            'queue' => 'fuseplug_laravel'
        ]);
        $operation = DB::table('operations')->orderBy('id', 'desc')->first();

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
        $operation_rule = DB::table('operation_rules')->orderBy('id', 'desc')->first();

        DB::table('operation_actions')->insert([
            'operation_rule_id' => $operation_rule->id,
            'order' => 1,
            'name' => 'hit foaas company endpoint',
            'operation_type' => 'http',
            'operation_source' => 'fuse',
            'http_verb' => 'GET'
        ]);
        $operation_action = DB::table('operation_actions')->orderBy('id', 'desc')->first();

        DB::table('data_mappings')->insert([
            'operation_action_id' => $operation_action->id,
            'brand_versions' => 'v1,v2',
            'template' => '{brand_root_url}/anyway/{company}/{from}',
            'fuse_versions' => '57-59'
        ]);
        $data_mapping = DB::table('data_mappings')->orderBy('id', 'desc')->first();

        DB::table('data_mapping_details')->insert([
            'data_mapping_id' => $data_mapping->id,
            'order' => 1,
            'source_field' => 'from',
            'source_field_type' => 'payload',
            'target_field' => 'from',
            'target_data_type' => 'url',
            'default_value' => 'somebody important'
        ]);
        $data_mapping_detail = DB::table('data_mapping_details')->orderBy('id', 'desc')->first();

        DB::table('data_mapping_details')->insert([
            'data_mapping_id' => $data_mapping->id,
            'order' => 2,
            'source_field' => 'company',
            'source_field_type' => 'url',
            'target_field' => 'company',
            'target_data_type' => 'url',
            'skip_if_empty' => true,
            'default_value' => 'Honda of America'
        ]);
        $data_mapping_detail = DB::table('data_mapping_details')->orderBy('id', 'desc')->first();

        DB::table('data_mapping_details')->insert([
            'data_mapping_id' => $data_mapping->id,
            'order' => 3,
            'source_field' => 'TEST_CLIENT_ROOT_URL',
            'target_field' => 'brand_root_url',
            'target_data_type' => 'url',
            'transform' => 'env_variable',
            'default_value' => 'http://it_didnt_get_set.com'
        ]);
        $data_mapping_detail = DB::table('data_mapping_details')->orderBy('id', 'desc')->first();

        DB::table('operations')->insert([
            'brand_id' => $test_brand->id,
            'name' => 'credit_check_python',
            'queue' => 'fuseplug_python'
        ]);
        $operation = DB::table('operations')->orderBy('id', 'desc')->first();

        DB::table('operation_rules')->insert([
            'operation_id' => $operation->id,
            'brand_version' => 'v2',
            'fuse_version' => '58',
            'order' => 1,
            'acting_on' => 'request',
            'do_always' => false,
            'input_selector' => 'def',
            'operator' => '=',
            'allowed_value' => 'spe'
        ]);
        $operation_rule = DB::table('operation_rules')->orderBy('id', 'desc')->first();

        DB::table('operation_actions')->insert([
            'operation_rule_id' => $operation_rule->id,
            'order' => 1,
            'name' => 'send_stuff_to_whatever_python',
            'operation_type' => 'http',
            'operation_source' => 'fuse',
            'http_verb' => 'GET'
        ]);
        $operation_action = DB::table('operation_actions')->orderBy('id', 'desc')->first();

        DB::table('data_mappings')->insert([
            'operation_action_id' => $operation_action->id,
            'brand_versions' => 'v1,v2',
            'template' => 'http://idontcare.com/see/if/i/care',
            'fuse_versions' => '57-59'
        ]);
        $data_mapping = DB::table('data_mappings')->orderBy('id', 'desc')->first();

        DB::table('data_mapping_details')->insert([
            'data_mapping_id' => $data_mapping->id,
            'order' => 1,
            'source_field' => 'from',
            'target_field' => 'from',
            'target_data_type' => 'url',
            'default_value' => 'somebody important'
        ]);
        $data_mapping_detail = DB::table('data_mapping_details')->orderBy('id', 'desc')->first();

        // make mock endpoint for post
        DB::table('operations')->insert([
            'brand_id' => $mock_brand->id,
            'name' => 'mock_post_endpoint'
        ]);
        $operation = DB::table('operations')->orderBy('id', 'desc')->first();

        DB::table('operation_rules')->insert([
            'operation_id' => $operation->id,
            'brand_version' => 'dontcare',
            'fuse_version' => 'dontcare',
            'order' => 1,
            'acting_on' => 'dontcare',
            'do_always' => true,
            'input_selector' => 'dontcare',
            'operator' => '=',
            'allowed_value' => 'dontcare'
        ]);
        $operation_rule = DB::table('operation_rules')->orderBy('id', 'desc')->first();

        DB::table('operation_actions')->insert([
            'operation_rule_id' => $operation_rule->id,
            'order' => 1,
            'name' => 'mock_post_operation',
            'operation_type' => 'mock',
            'operation_source' => 'dontcare',
            'extra_parameters' => '{"sleep_time_milliseconds": "2000"}',
            'http_verb' => 'POST'
        ]);
        $operation_action = DB::table('operation_actions')->orderBy('id', 'desc')->first();

        DB::table('data_mappings')->insert([
            'operation_action_id' => $operation_action->id,
            'brand_versions' => 'dontcare',
            'fuse_versions' => 'dontcare',
            'template' => '{"this_is_from": "{from}", "processed_at": "{current_datetime}", "test_url": "{test_root_url}"}'
        ]);
        $data_mapping = DB::table('data_mappings')->orderBy('id', 'desc')->first();

        DB::table('data_mapping_details')->insert([
            'data_mapping_id' => $data_mapping->id,
            'order' => 1,
            'source_field' => 'from',
            'source_field_type' => 'payload',
            'target_field' => 'from',
            'target_data_type' => 'payload',
            'default_value' => 'some guy'
        ]);
        $data_mapping_detail = DB::table('data_mapping_details')->orderBy('id', 'desc')->first();
        DB::table('data_mapping_details')->insert([
            'data_mapping_id' => $data_mapping->id,
            'order' => 2,
            'source_field' => '',
            'source_field_type' => '',
            'target_field' => 'current_datetime',
            'target_data_type' => 'payload',
            'target_format_string' => 'M d, Y D H:m:s',
            'transform' => 'php_format_date',
            'default_value' => ''
        ]);
        $data_mapping_detail = DB::table('data_mapping_details')->orderBy('id', 'desc')->first();
        DB::table('data_mapping_details')->insert([
            'data_mapping_id' => $data_mapping->id,
            'order' => 3,
            'source_field' => 'TEST_CLIENT_ROOT_URL',
            'source_field_type' => '',
            'target_field' => 'test_root_url',
            'target_data_type' => 'payload',
            'transform' => 'env_variable',
            'default_value' => 'http://im_a_luzer.com'
        ]);
        $data_mapping_detail = DB::table('data_mapping_details')->orderBy('id', 'desc')->first();
    }
}
