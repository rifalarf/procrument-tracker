<?php

namespace App\Exports;

use App\Models\ProcurementItem;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProcurementExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ProcurementItem::all();
    }
}
