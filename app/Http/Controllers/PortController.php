<?php

namespace App\Http\Controllers;

use App\Models\Port;
use Illuminate\Http\Request;

class PortController extends Controller
{
    public function index(Request $request)
    {
        // Fitur pencarian pelabuhan
        $query = Port::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->get()
        ]);
    }
}