<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DataMappingDetail;

class DataMapping extends Model
{
    public function transform($call, $operation_action) {
        $data_mapping_details = DataMappingDetail::where('data_mapping_id', $this->id)->orderBy('order')->get();
        $source_data = json_decode($call->request_data, true);
        $source_data_payload = $source_data['payload'];
        $source_data_get_parameters = $source_data['get_parameters'];
        if ($operation_action->http_verb == 'GET') {
            // we're building a url
            if ($operation_action->operation_source =='fuse') {
                $target_url = $operation_action->brand_url;
            }
            if (($operation_action->operation_type == 'http') || 
                ($operation_action->operation_type == 'format_data')) {
                foreach ($data_mapping_details as $data_mapping_detail) {
                    if ($data_mapping_detail->target_data_type == 'url') {
                        // take it from source_field, urlencode it, move it to target_field
                        $replace_with_value = '';
                        $replacement_selector = 'BEFUDDLED aRACHNId'; // some value that won't be in a url, even as a get parm
                        if (isset($data_mapping_detail->default_value)) {
                            $replace_with_value = $data_mapping_detail->default_value;
                        }
                        if ($data_mapping_detail->source_field_type == 'payload') {
                            if ((isset($data_mapping_detail->source_field)) && 
                                (isset($source_data_payload[$data_mapping_detail->source_field]))) {
                                $replace_with_value = $source_data_payload[$data_mapping_detail->source_field];
                            }
                        } else { // its a get parameter
                            if ((isset($data_mapping_detail->source_field)) && 
                                (isset($source_data_get_parameters[$data_mapping_detail->source_field]))) {
                                $replace_with_value = $source_data_get_parameters[$data_mapping_detail->source_field];
                            }
                        }
                        $replace_with_value = rawurlencode($replace_with_value);
                        if (isset($data_mapping_detail->target_field)) {
                            $replacement_selector = '{' . $data_mapping_detail->target_field . '}';
                        }
                        $target_url = str_replace($replacement_selector, $replace_with_value, $target_url);
                    }
                }
            }
            return $target_url;
        } else {
            // we're building post data
        }
    }
}
