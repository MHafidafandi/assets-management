<?php

namespace App\Jobs;

use App\Imports\UsersImport;
use App\Models\DataJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ProcessUserImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $dataJobId,
        public string $filePath
    ) {}

    public function handle(): void
    {
        $dataJob = DataJob::findOrFail($this->dataJobId);
        $dataJob->update(['status' => 'processing']);

        try {
            $import = new UsersImport();
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
