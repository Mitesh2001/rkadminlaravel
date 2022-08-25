<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Event;

class ClientExport implements FromCollection,WithHeadings
{

    function __construct($id) {
      $this->id = $id;
    }

    public function headings(): array
    {
        return [
            'SR.',
            'Client Name',
            'Company Name',
            'Plan Name',
            'Plan Price',
            'Plan Expiry Date',
            'City'
        ];
    }
    
    public function collection()
    {
      return $this->id;
    }
}
