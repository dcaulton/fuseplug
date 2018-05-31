<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Operation;
use App\Models\OperationRule;
use App\Models\OperationAction;
use App\Models\DataMapping;
use App\Models\DataMappingDetail;

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


        // make mock endpoint for post echo
        DB::table('operations')->insert([
            'brand_id' => $mock_brand->id,
            'name' => 'mock_post_echo_endpoint'
        ]);
        $echo_post_mock_operation = DB::table('operations')->orderBy('id', 'desc')->first();

        DB::table('operation_rules')->insert([
            'operation_id' => $echo_post_mock_operation->id,
            'brand_version' => 'dontcare',
            'fuse_version' => 'dontcare',
            'order' => 1,
            'acting_on' => 'dontcare',
            'do_always' => true,
            'input_selector' => 'dontcare',
            'operator' => '=',
            'allowed_value' => 'dontcare'
        ]);
        $mock_post_operation_rule = DB::table('operation_rules')->orderBy('id', 'desc')->first();

        DB::table('operation_actions')->insert([
            'operation_rule_id' => $mock_post_operation_rule->id,
            'order' => 1,
            'name' => 'mock_post_echo_operation_action',
            'operation_type' => 'mock',
            'operation_source' => 'dontcare',
            'extra_parameters' => '{"sleep_time_min_milliseconds": "500", "sleep_time_max_milliseconds": "2000"}',
            'http_verb' => 'POST'
        ]);
        $mock_post_operation_action = DB::table('operation_actions')->orderBy('id', 'desc')->first();

        DB::table('data_mappings')->insert([
            'operation_action_id' => $mock_post_operation_action->id,
            'brand_versions' => 'dontcare',
            'fuse_versions' => 'dontcare',
            'object_type_being_created' => 'echo',
            'template' => ''
        ]);
        $data_mapping = DB::table('data_mappings')->orderBy('id', 'desc')->first();


        // make mock endpoint for post with mapping
        DB::table('operations')->insert([
            'brand_id' => $mock_brand->id,
            'name' => 'mock_post_endpoint_1'
        ]);
        $mock_post_operation = DB::table('operations')->orderBy('id', 'desc')->first();

        DB::table('operation_rules')->insert([
            'operation_id' => $mock_post_operation->id,
            'brand_version' => 'dontcare',
            'fuse_version' => 'dontcare',
            'order' => 1,
            'acting_on' => 'dontcare',
            'do_always' => true,
            'input_selector' => 'dontcare',
            'operator' => '=',
            'allowed_value' => 'dontcare'
        ]);
        $mock_post_operation_rule = DB::table('operation_rules')->orderBy('id', 'desc')->first();

        DB::table('operation_actions')->insert([
            'operation_rule_id' => $mock_post_operation_rule->id,
            'order' => 1,
            'name' => 'mock_post_operation',
            'operation_type' => 'mock',
            'operation_source' => 'dontcare',
            'extra_parameters' => '{"sleep_time_min_milliseconds": "500", "sleep_time_max_milliseconds": "2000"}',
            'http_verb' => 'POST'
        ]);
        $mock_post_operation_action = DB::table('operation_actions')->orderBy('id', 'desc')->first();

        DB::table('data_mappings')->insert([
            'operation_action_id' => $mock_post_operation_action->id,
            'brand_versions' => 'dontcare',
            'fuse_versions' => 'dontcare',
            'object_type_being_created' => 'payload',
            'template' => '{"caller": "{from}", "processed_at": "{current_datetime}", "test_url": "{test_root_url}"}'
        ]);
        $data_mapping = DB::table('data_mappings')->orderBy('id', 'desc')->first();

        DB::table('data_mapping_details')->insert([
            'data_mapping_id' => $data_mapping->id,
            'order' => 1,
            'source_field' => 'subtitle',
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


        // make mock endpoint for get
        DB::table('operations')->insert([
            'brand_id' => $mock_brand->id,
            'name' => 'mock_get_endpoint_1'
        ]);
        $mock_get_operation = DB::table('operations')->orderBy('id', 'desc')->first();

        DB::table('operation_rules')->insert([
            'operation_id' => $mock_get_operation->id,
            'brand_version' => 'dontcare',
            'fuse_version' => 'dontcare',
            'order' => 1,
            'acting_on' => 'dontcare',
            'do_always' => true,
            'input_selector' => 'dontcare',
            'operator' => '=',
            'allowed_value' => 'dontcare'
        ]);
        $mock_get_operation_rule = DB::table('operation_rules')->orderBy('id', 'desc')->first();

        DB::table('operation_actions')->insert([
            'operation_rule_id' => $mock_get_operation_rule->id,
            'order' => 1,
            'name' => 'mock_get_operation',
            'operation_type' => 'mock',
            'operation_source' => 'dontcare',
            'extra_parameters' => '{"sleep_time_min_milliseconds": "250", "sleep_time_max_milliseconds": "500"}',
            'http_verb' => 'GET'
        ]);
        $mock_get_operation_action = DB::table('operation_actions')->orderBy('id', 'desc')->first();

        DB::table('data_mappings')->insert([
            'operation_action_id' => $mock_get_operation_action->id,
            'brand_versions' => 'dontcare',
            'fuse_versions' => 'dontcare',
            'object_type_being_created' => 'payload',
            'template' => '{"processed_at": "{current_datetime}"}'
        ]);
        $data_mapping = DB::table('data_mappings')->orderBy('id', 'desc')->first();

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








        // live endpoint for laravel - one get to foaas anyway (2 parms) one post to a mock endpoint
        DB::table('operations')->insert([
            'brand_id' => $test_brand->id,
            'name' => 'credit_check_2ops',
            'queue' => 'fuseplug'
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
            'name' => 'GET to foaas',
            'operation_type' => 'http',
            'operation_source' => 'fuse',
            'http_verb' => 'GET'
        ]);
        $operation_action = DB::table('operation_actions')->orderBy('id', 'desc')->first();

        DB::table('data_mappings')->insert([
            'operation_action_id' => $operation_action->id,
            'brand_versions' => 'v1,v2',
            'template' => '{brand_root_url}/anyway/{company}/{from}',
            'object_type_being_created' => 'url',
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

        DB::table('operation_actions')->insert([
            'operation_rule_id' => $operation_rule->id,
            'order' => 2,
            'name' => 'POST to mock endpoint',
            'operation_type' => 'http',
            'operation_source' => 'fuse',
            'http_verb' => 'POST'
        ]);
        $operation_action = DB::table('operation_actions')->orderBy('id', 'desc')->first();

        DB::table('data_mappings')->insert([
            'operation_action_id' => $operation_action->id,
            'brand_versions' => 'v1,v2',
            'template' => 'http://localhost:8000/mock/' . $mock_post_operation->id,
            'object_type_being_created' => 'url',
            'fuse_versions' => '57-59'
        ]);
        $data_mapping = DB::table('data_mappings')->orderBy('id', 'desc')->first();

        DB::table('data_mappings')->insert([
            'operation_action_id' => $operation_action->id,
            'brand_versions' => 'v1,v2',
            'template' => '{"subtitle": "{subtitle}"}',
            'object_type_being_created' => 'payload',
            'fuse_versions' => '57-59'
        ]);
        $data_mapping = DB::table('data_mappings')->orderBy('id', 'desc')->first();

        DB::table('data_mapping_details')->insert([
            'data_mapping_id' => $data_mapping->id,
            'order' => 1,
            'source_field' => 'subtitle',
            'source_field_type' => 'payload',
            'target_field' => 'subtitle',
            'target_data_type' => 'payload',
            'default_value' => 'many people speaking at once'
        ]);
        $data_mapping_detail = DB::table('data_mapping_details')->orderBy('id', 'desc')->first();


        // live endpoint for laravel - one get to foaas cool (1 parm)
        DB::table('operations')->insert([
            'brand_id' => $test_brand->id,
            'name' => 'credit_check_1op_cool',
            'queue' => 'fuseplug_laravel'
        ]);
        $laravel_cool_operation = DB::table('operations')->orderBy('id', 'desc')->first();

        DB::table('operation_rules')->insert([
            'operation_id' => $laravel_cool_operation->id,
            'brand_version' => 'v3',
            'fuse_version' => '57',
            'order' => 1,
            'acting_on' => 'request',
            'do_always' => false,
            'input_selector' => 'abc',
            'operator' => '=',
            'allowed_value' => 'special'
        ]);
        $laravel_cool_operation_rule = DB::table('operation_rules')->orderBy('id', 'desc')->first();

        DB::table('operation_actions')->insert([
            'operation_rule_id' => $laravel_cool_operation_rule->id,
            'order' => 1,
            'name' => 'hurdaherr get to cool story bro',
            'operation_type' => 'http',
            'operation_source' => 'fuse',
            'http_verb' => 'GET'
        ]);
        $laravel_cool_operation_action = DB::table('operation_actions')->orderBy('id', 'desc')->first();

        DB::table('data_mappings')->insert([
            'operation_action_id' => $laravel_cool_operation_action->id,
            'brand_versions' => 'v1,v2',
            'template' => 'http://foaas.com/cool/{from}',
            'object_type_being_created' => 'url',
            'fuse_versions' => '57-59'
        ]);
        $laravel_cool_data_mapping = DB::table('data_mappings')->orderBy('id', 'desc')->first();

        DB::table('data_mapping_details')->insert([
            'data_mapping_id' => $laravel_cool_data_mapping->id,
            'order' => 1,
            'source_field' => 'from',
            'source_field_type' => 'payload',
            'target_field' => 'from',
            'target_data_type' => 'url',
            'default_value' => 'nobody you know'
        ]);
        $laravel_cool_data_mapping_detail = DB::table('data_mapping_details')->orderBy('id', 'desc')->first();





        // live endpoint for laravel - one post to our mock that just echoes
        DB::table('operations')->insert([
            'brand_id' => $test_brand->id,
            'name' => 'credit_check_1post_echo',
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
            'name' => 'do a post to our local mock that just echoes back',
            'operation_type' => 'http',
            'operation_source' => 'fuse',
            'http_verb' => 'POST'
        ]);
        $operation_action = DB::table('operation_actions')->orderBy('id', 'desc')->first();

        DB::table('data_mappings')->insert([
            'operation_action_id' => $operation_action->id,
            'brand_versions' => 'v1,v2',
            'template' => 'http://localhost:8000/mock/' . $echo_post_mock_operation->id,
            'object_type_being_created' => 'url',
            'fuse_versions' => '57-59'
        ]);
        $data_mapping = DB::table('data_mappings')->orderBy('id', 'desc')->first();

        DB::table('data_mappings')->insert([
            'operation_action_id' => $operation_action->id,
            'brand_versions' => 'v1,v2',
            'template' => '{"wickedness": "{from}"}',
            'object_type_being_created' => 'payload',
            'fuse_versions' => '57-59'
        ]);
        $data_mapping = DB::table('data_mappings')->orderBy('id', 'desc')->first();

        DB::table('data_mapping_details')->insert([
            'data_mapping_id' => $data_mapping->id,
            'order' => 1,
            'source_field' => 'from',
            'source_field_type' => 'payload',
            'target_field' => 'from',
            'target_data_type' => 'payload',
            'default_value' => 'nobody you know'
        ]);
        $data_mapping_detail = DB::table('data_mapping_details')->orderBy('id', 'desc')->first();

    }
}
