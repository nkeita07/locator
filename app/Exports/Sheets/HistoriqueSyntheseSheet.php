<?php

namespace App\Exports\Sheets;

use App\Models\Article;
use App\Models\HistoriqueStock;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HistoriqueSyntheseSheet implements FromArray, WithHeadings
{
    public function array(): array
    {
        $stockTotal = Article::sum('stock');
        $stockAdresse = HistoriqueStock::latest()
            ->select('stock_total_adresse')
            ->value('stock_total_adresse') ?? 0;

        $taux = $stockTotal > 0
            ? round(($stockAdresse / $stockTotal) * 100, 2)
            : 0;

        return [[
            $stockTotal,
            $stockAdresse,
            $taux
        ]];
    }

    public function headings(): array
    {
        return [
            'Stock total articles',
            'Stock total adressé',
            'Taux global (%)'
        ];
    }
}
