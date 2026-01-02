<?php

namespace App\Exports;

use App\Models\HistoriqueStock;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HistoriqueStockExport implements FromCollection, WithHeadings
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = HistoriqueStock::query()->orderByDesc('created_at');

        if ($this->request->filled('reference')) {
            $query->where('reference_article', $this->request->reference);
        }
        if ($this->request->filled('zone')) {
            $query->where('zone', $this->request->zone);
        }
        if ($this->request->filled('action')) {
            $query->where('action_type', $this->request->action);
        }
        if ($this->request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $this->request->date_start);
        }
        if ($this->request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $this->request->date_end);
        }

        return $query->get([
            'created_at',
            'reference_article',
            'designation_article',
            'zone',
            'action_type',
            'quantite',
            'stock_avant',
            'stock_apres',
            'taux_adressage',
            'nom_collaborateur'
        ]);
    }

    public function headings(): array
    {
        return [
            'Date',
            'Référence',
            'Désignation',
            'Zone',
            'Action',
            'Quantité',
            'Stock avant',
            'Stock après',
            'Taux (%)',
            'Utilisateur'
        ];
    }
}
