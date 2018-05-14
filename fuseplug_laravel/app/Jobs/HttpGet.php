<?php

namespace App\Jobs;

use App\Services\HttpGetService;
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
        HttpGetService::processRequest($this->super_call_id);
    }
}
