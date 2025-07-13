<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Division;
use App\Http\Resources\DivisionResource;

class DivisionController extends Controller
{
    public function index(Request $request)
    {

        $filter = $request->validate([
            'name' => 'string',
        ]);

        if (isset($filter['name'])) {
            $divisions = Division::where('name', 'like', '%' . $filter['name'] . '%')->paginate(10);
        } else {
            $divisions = Division::paginate(10);
        }

        return new DivisionResource('success', 'Berhasil mengambil data divisi',$divisions);
    }
}
