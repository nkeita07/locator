<?php

namespace App\Exports;

use Illuminate\Http\Request;
use App\Models\HistoriqueStock;
use App\Models\Article;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class HistoriqueExport implements WithMultipleSheets
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function sheets(): array
    {
        return [
            new Sheets\HistoriqueDetailSheet($this->request),
            new Sheets\HistoriqueSyntheseSheet($this->request),
        ];
    }
}
