<?php

namespace App\Exports;

use App\Models\Admin;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection
{
    /**
     * Return the collection of users
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Admin::select('*')->get(); // Or add your custom query to fetch specific data
    }
}
