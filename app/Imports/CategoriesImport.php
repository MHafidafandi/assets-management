<?php

namespace App\Imports;

use App\Exports\CategoriesExport;
use App\Models\Category;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CategoriesImport implements SkipsOnFailure, ToModel, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;

    public int $successCount = 0;

    private function getFieldMap(): array
    {
        $map = [];
        foreach (CategoriesExport::AVAILABLE_FIELDS as $dbField => $label) {
            $map[Str::slug($label, '_')] = $dbField;
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
                if ($dbField === 'is_active') {
                    $mappedData['is_active'] = filter_var($value ?? true, FILTER_VALIDATE_BOOLEAN);
                } else {
                    $mappedData[$dbField] = $value;
                }
            }
        }

        $this->successCount++;

        return new Category($mappedData);
    }

    public function rules(): array
    {
        $map = $this->getFieldMap();

        $nameHeader = array_search('name', $map);

        return [
            $nameHeader => ['required', 'string', 'max:255', 'unique:categories,name'],
        ];
    }
}
