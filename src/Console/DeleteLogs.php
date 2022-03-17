<?php

namespace GomaGaming\Logs\Console;

use Illuminate\Console\Command;
use GomaGaming\Logs\Models\Log;
use GomaGaming\Logs\Models\LogMetaData;

class DeleteLogs extends Command
{
    protected $signature = 'gglogs:purge {--days=}';

    protected $description = 'Delete old Logs';

    protected $days;

    public function handle()
    {
        $this->info('Starting to delete logs...');

        $this->days = $this->option('days');

        $this->deleteOldLogs();

        $this->info('Logs deleted with Success!');
    }

    private function deleteOldLogs()
    {
        $query = Log::orderBy('created_at');

        if($this->days)
        {
            $date = date('Y-m-d', strtotime('-' . $this->days . ' day', strtotime(date('Y-m-d'))));

            $query = $query->whereDate('created_at', '<=', $date);
        }

        $logs = $query->get();

        foreach ($logs as $log) 
        {
            $log->metadata()->delete();
            $log->delete();
        }
    }
}