<?php

namespace App\Http\Controllers;

use App\Models\Port;
use Illuminate\Http\Request;

class PortController extends Controller
{
    public function index() {
        // 1. Mengambil data pelabuhan dan di-JOIN dengan nama negaranya
        $ports = Port::join('countries', 'ports.country_id', '=', 'countries.id')
                    ->select('ports.name', 'ports.latitude', 'ports.longitude', 'countries.name as country_name')
                    ->get();
                    
        // 2. Mengirim variabel $ports ke file port.blade.php
        return view('port', compact('ports'));
    }
}