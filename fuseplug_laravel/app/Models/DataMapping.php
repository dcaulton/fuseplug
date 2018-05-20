<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DataMappingDetail;

class DataMapping extends Model
{
    private function get_replace_with_value($data_mapping_detail, $source_data) {
        $replace_with_value = '';
        if (isset($data_mapping_detail->default_value)) {
            $replace_with_value = $data_mapping_detail->default_value;
        }
        if ($data_mapping_detail->source_field_type == 'payload') {
            if ((isset($data_mapping_detail->source_field)) && 
                    (isset($source_data['payload'][$data_mapping_detail->source_field]))) {
                $replace_with_value = $source_data['payload'][$data_mapping_detail->source_field];
            }
        } elseif ($data_mapping_detail->source_field_type == 'url') {
            // TODO we should urldecode this
            if ((isset($data_mapping_detail->source_field)) && 
                    (isset($source_data['get_parameters'][$data_mapping_detail->source_field]))) {
                $replace_with_value = $source_data['get_parameters'][$data_mapping_detail->source_field];
            }
        } else {
            if ($data_mapping_detail->transform == 'php_format_date') {
                $date_format_string = 'm-d-Y h:i:s';
                if (!isset($data_mapping_detail->target_format_string)) {
                    $date_format_string = $data_mapping_detail->target_format_string;
                } 
                $replace_with_value = date($date_format_string);
            }
            if (($data_mapping_detail->transform == 'env_variable') && 
                    (isset($data_mapping_detail->source_field))) {
                $default_value = '';
                if (isset($data_mapping_detail->default_value)) {
                    $default_value = $data_mapping_detail->default_value;
                }
                $replace_with_value = env($data_mapping_detail->source_field, $default_value);
            }
        }
        return $replace_with_value;
    }

    private function build_target_selector($data_mapping_detail) {
        $replacement_selector = 'BEFUDDLED aRACHNId'; // some value that won't be in a url, even as a get parm
        if (isset($data_mapping_detail->target_field)) {
            $replacement_selector = '{' . $data_mapping_detail->target_field . '}';
        }
        return $replacement_selector;
    }

    public function transform($request_data, $operation_action) {
        $data_mapping_details = DataMappingDetail::where('data_mapping_id', $this->id)->orderBy('order')->get();
        $source_data = json_decode($request_data, true);

        $return_data = $this->template;

        foreach ($data_mapping_details as $data_mapping_detail) {
            $replace_with_value = $this->get_replace_with_value($data_mapping_detail, $source_data);
            if ($data_mapping_detail->target_data_type == 'url') {
                if ($data_mapping_detail->transform != 'env_variable') {
                    // stuff coming from the the env file is considered safe for a url
                    //   otherwise specifying root urls in env isn't very human-readable
                    $replace_with_value = rawurlencode($replace_with_value);
                }
            } 
            $replacement_selector = $this->build_target_selector($data_mapping_detail);
            $return_data = str_replace($replacement_selector, $replace_with_value, $return_data);
        }

        return $return_data;
    }
}
