<?php

namespace App\Imports;

use App\Models\Driver;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DriversImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Driver([
            'name'=>$row['name'],
            'national_id'=>$row['national_id'],
            'line_id'=>$row['line_id'],
            'phone'=>$row['phone'] ?? null
        ]);
    }
}
