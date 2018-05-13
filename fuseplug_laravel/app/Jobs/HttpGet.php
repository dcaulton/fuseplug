<?php

namespace App\Jobs;

use App\Models\SuperCall;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class HttpGet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $super_call_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($super_call_id)
    {
        $this->super_call_id = $super_call_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $super_call = SuperCall::find($this->super_call_id);
        $target_url = 'http://foaas.com/cool/';
        $super_call = SuperCall::find($this->super_call_id);
        $from = 'Dave';
        $initial_payload_array = json_decode($super_call->initial_payload, true);
        if (array_key_exists('from', $initial_payload_array)) {
            $from = rawurlencode($initial_payload_array['from']);
        }
        $target_url .= $from;
        try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $target_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Content-Type: application/json",
				"Accept: application/json" 
			));
			$data = curl_exec($ch);
			curl_close($ch);
			$super_call->final_response = $data;
			$super_call->status = 'COMPLETE';
			$super_call->save();
        } catch (\Exception $e) {
            echo "caught exception in HttpGet: " . $e->getMessage() . "\n";
			$super_call->status = 'FAILED';
			$super_call->save();
        }
    }
}
