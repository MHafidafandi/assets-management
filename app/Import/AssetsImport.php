<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\Category;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AssetsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public int $successCount = 0;

    private function getFieldMap(): array
    {
        $map = [];
        foreach (\App\Exports\AssetsExport::AVAILABLE_FIELDS as $dbField => $label) {
            $map[\Illuminate\Support\Str::slug($label, '_')] = $dbField;
        }
        return $map;
    }

    public function model(array $row)
    {
        $map = $this->getFieldMap();
        $mappedData = [];

        foreach ($row as $header => $value) {
            if (isset($map[$header])) {
                $dbField = $map[$header];
                if ($dbField === 'category') {
                    if ($value) {
                        $category = Category::where('name', $value)->first();
                        $mappedData['category_id'] = $category?->id;
                    }
                } elseif ($dbField === 'purchase_date') {
                    $mappedData['purchase_date'] = $value ? Carbon::parse($value) : null;
                } elseif ($dbField === 'is_active') {
                    $mappedData['is_active'] = filter_var($value ?? true, FILTER_VALIDATE_BOOLEAN);
                } elseif ($dbField === 'specification') {
                    $mappedData['specification'] = $this->parseSpecification($value);
                } else {
                    $mappedData[$dbField] = $value;
                }
            }
        }

        $this->successCount++;

        return new Asset($mappedData);
    }

    public function rules(): array
    {
        $map = $this->getFieldMap();
        
        $nameHeader = array_search('name', $map);
        $categoryHeader = array_search('category', $map);
        $purchaseDateHeader = array_search('purchase_date', $map);

        return [
            $nameHeader => ['required', 'string', 'max:255'],
            $categoryHeader => ['required', function ($attribute, $value, $fail) {
                if (! Category::where('name', $value)->exists()) {
                    $fail("Kategori \"{$value}\" tidak ditemukan.");
                }
            }],
            $purchaseDateHeader => ['required'],
        ];
    }

    /**
     * Format sel: "merk=Dell;warna=Hitam" -> ['merk' => 'Dell', 'warna' => 'Hitam']
     */
    private function parseSpecification(?string $raw): array
    {
        if (empty($raw)) {
            return [];
        }

        $result = [];
        foreach (explode(';', $raw) as $pair) {
            [$key, $value] = array_pad(explode('=', $pair, 2), 2, null);
            if ($key) {
                $result[trim($key)] = trim((string) $value);
            }
        }

        return $result;
    }
}
