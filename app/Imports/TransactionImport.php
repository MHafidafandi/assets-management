<?php

namespace App\Imports;

use App\Exports\TransactionExport;
use App\Models\Asset;
use App\Models\AssetTransaction;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;


class TransactionImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public int $successCount = 0;
    private function getFieldMap(): array
    {
        $map = [];
        foreach (TransactionExport::AVAILABLE_FIELDS as $dbField => $label) {
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
                if ($dbField === 'borrowed_at' || $dbField === 'returned_at') {
                    $mappedData[$dbField] = $value ? \Carbon\Carbon::parse($value) : null;
                } elseif ($dbField === 'user') {
                    $mappedData['user_id'] = $value ? \App\Models\User::where('name', $value)->first()?->id : null;
                } elseif ($dbField === 'asset') {
                    $mappedData['asset_id'] = $value ? \App\Models\Asset::where('name', $value)->first()?->id : null;
                } elseif ($dbField === 'is_returned') {
                    $mappedData[$dbField] = filter_var($value ?? false, FILTER_VALIDATE_BOOLEAN);
                } elseif ($dbField === 'notes') {
                    $mappedData[$dbField] = json_decode($value, true) ?: [];
                } else {
                    $mappedData[$dbField] = $value;
                }
            }
        }
        $this->successCount++;
        return new AssetTransaction($mappedData);
    }

    public function rules(): array
    {
        $map = $this->getFieldMap();

        $assetField = array_search('asset', $map);
        $userField = array_search('user', $map);
        $borowedAtField = array_search('borrowed_at', $map);

        return [
            $assetField => ['required', 'exists:assets,name'],
            $userField => ['required', 'exists:users,name'],
            $borowedAtField => ['required', 'date'],
        ];
    }
}
