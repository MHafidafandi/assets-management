<?php

namespace App\Jobs;

use App\Models\DataJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TransactionImport;

class ProcessTransactionImport implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $dataJobId,
        public string $filePath
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $dataJob = DataJob::findOrFail($this->dataJobId);
        $dataJob->update(['status' => 'processing']);

        try {
            $import = new TransactionImport();
            Excel::import($import, $this->filePath);

            $failures = $import->failures();

            $dataJob->update([
                'status'  => 'completed',
                'summary' => [
                    'total_rows' => $import->successCount + $failures->count(),
                    'success'    => $import->successCount,
                    'failed'     => $failures->count(),
                    'errors'     => $failures->map(fn($f) => [
                        'row'       => $f->row(),
                        'attribute' => $f->attribute(),
                        'errors'    => $f->errors(),
                    ])->values()->toArray(),
                ],
            ]);
        } catch (\Throwable $e) {
            $dataJob->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
