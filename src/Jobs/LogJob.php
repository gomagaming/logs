<?php

namespace GomaGaming\Logs\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use GomaGaming\Logs\Services\LogService;

class LogJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(LogService $logService)
    {
        if (config('gomagaminglogs.process_jobs')) {
            $logService->process($this->data);
        }
    }  
}
