<?php

namespace App\Exports;

use App\Models\AssetTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public const AVAILABLE_FIELDS = [
        'user' => 'User',
        'asset' => 'Asset',
        'borrowed_at' => 'Borrowed At',
        'returned_at' => 'Returned At',
        'is_returned' => 'Is Returned',
        'notes' => 'Notes',
    ];

    public function __construct(private array $fields = [])
    {
        if (empty($this->fields)) {
            $this->fields = array_keys(self::AVAILABLE_FIELDS);
        }
    }

    public function query()
    {
        return AssetTransaction::query()->with(['user', 'asset'])->orderBy('borrowed_at', 'desc');
    }

    public function headings(): array
    {
        return array_map(fn ($key) => self::AVAILABLE_FIELDS[$key], $this->fields);
    }

    public function map($transaction): array
    {
        return array_map(function ($field) use ($transaction) {
            return match ($field) {
                'user' => $transaction->user->name ?? '',
                'asset' => $transaction->asset->name ?? '',
                'borrowed_at' => $transaction->borrowed_at?->format('Y-m-d H:i'),
                'returned_at' => $transaction->returned_at?->format('Y-m-d H:i'),
                'is_returned' => $transaction->is_returned ? 'Yes' : 'No',
                'notes' => json_encode($transaction->notes),
                default => '',
            };
        }, $this->fields);
    }
}
