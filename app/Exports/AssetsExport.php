<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AssetsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    // Semua kolom yang tersedia untuk dipilih
    public const AVAILABLE_FIELDS = [
        'name'          => 'Nama Aset',
        'category'      => 'Kategori',
        'purchase_date' => 'Tanggal Pembelian',
        'is_active'     => 'Status',
        'specification' => 'Spesifikasi',
        'created_at'    => 'Dibuat Pada',
    ];

    public function __construct(private array $fields = [])
    {
        // Kalau tidak ada field dipilih, export semua
        if (empty($this->fields)) {
            $this->fields = array_keys(self::AVAILABLE_FIELDS);
        }
    }

    public function query()
    {
        return Asset::query()->with('category')->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return array_map(fn($key) => self::AVAILABLE_FIELDS[$key], $this->fields);
    }

    public function map($asset): array
    {
        return array_map(function ($field) use ($asset) {
            return match ($field) {
                'name'          => $asset->name,
                'category'      => $asset->category?->name,
                'purchase_date' => $asset->purchase_date?->format('Y-m-d H:i'),
                'is_active'     => $asset->is_active ? 'Aktif' : 'Nonaktif',
                'specification' => json_encode($asset->specification),
                'created_at'    => $asset->created_at?->format('Y-m-d H:i'),
                default         => '',
            };
        }, $this->fields);
    }
}
