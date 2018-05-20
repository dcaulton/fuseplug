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
        } else { // its a get parameter
            if ((isset($data_mapping_detail->source_field)) && 
                (isset($source_data['get_parameters'][$data_mapping_detail->source_field]))) {
                $replace_with_value = $source_data['get_parameters'][$data_mapping_detail->source_field];
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

        if ($operation_action->http_verb == 'GET') {
            if (($operation_action->operation_type == 'http') || 
                ($operation_action->operation_type == 'format_data')) {
                foreach ($data_mapping_details as $data_mapping_detail) {
                    if ($data_mapping_detail->target_data_type == 'url') {
                        // take it from source_field, urlencode it, move it to target_field
                        $replace_with_value = $this->get_replace_with_value($data_mapping_detail, $source_data);
                        $replace_with_value = rawurlencode($replace_with_value);
                        $replacement_selector = $this->build_target_selector($data_mapping_detail);
                        $return_data = str_replace($replacement_selector, $replace_with_value, $return_data);
                    }
                }
            }
        } elseif ($operation_action->http_verb == 'POST') {
            $return_data = $this->template;
            foreach ($data_mapping_details as $data_mapping_detail) {
                if ($data_mapping_detail->target_data_type == 'payload') {
                    $replace_with_value = $this->get_replace_with_value($data_mapping_detail, $source_data);
                    $replacement_selector = $this->build_target_selector($data_mapping_detail);
                    $return_data = str_replace($replacement_selector, $replace_with_value, $return_data);
                }
            }
        }
        return $return_data;
    }
}
