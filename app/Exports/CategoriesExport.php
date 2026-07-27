<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CategoriesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public const AVAILABLE_FIELDS = [
        'name'      => 'Nama Kategori',
        'is_active' => 'Status',
        'created_at'=> 'Dibuat Pada',
    ];

    public function __construct(private array $fields = [])
    {
        if (empty($this->fields)) {
            $this->fields = array_keys(self::AVAILABLE_FIELDS);
        }
    }

    public function query()
    {
        return Category::query()->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return array_map(fn($key) => self::AVAILABLE_FIELDS[$key], $this->fields);
    }

    public function map($category): array
    {
        return array_map(function ($field) use ($category) {
            return match ($field) {
                'name'       => $category->name,
                'is_active'  => $category->is_active ? 'Aktif' : 'Nonaktif',
                'created_at' => $category->created_at?->format('Y-m-d H:i'),
                default      => '',
            };
        }, $this->fields);
    }
}
