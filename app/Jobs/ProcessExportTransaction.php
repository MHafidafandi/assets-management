<?php

namespace App\Jobs;

use App\Exports\TransactionExport;
use App\Models\DataJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ProcessExportTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $exportJobId, public array $fields = []) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $exportJob = DataJob::findOrFail($this->exportJobId);
        $exportJob->update(['status' => 'processing']);

        try {
            $fileName = 'transaction_export_'.now()->format('Ymd_His').'.xlsx';

            Excel::store(new TransactionExport($this->fields), $fileName, 'public');

            $exportJob->update([
                'status' => 'completed',
                'file_path' => $fileName,
            ]);
        } catch (\Throwable $e) {
            dd($e->getMessage());
            $exportJob->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
