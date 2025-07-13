<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Http\Resources\EmployeeResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'division_id' => 'string|exists:divisions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $filter = $validator->validated();

        $employeesQuery = Employee::query();

        if (isset($filter['name'])) {
            $employeesQuery->where('name', 'like', '%' . $filter['name'] . '%');
        }

        if (isset($filter['division_id'])) {
            $employeesQuery->where('division_id', $filter['division_id']);
        }

        $employees = $employeesQuery->with('division')->paginate(10);

        return new EmployeeResource('success', 'Berhasil mengambil data karyawan', $employees);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'   => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name'    => 'required',
            'phone'   => 'required|unique:employees,phone',
            'division' => 'required|exists:divisions,id',
            'position' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $image = $request->file('image');
        $image->storeAs('employees', $image->hashName());

        $data = [
            'image'       => $image->hashName(),
            'name'        => $request->input('name'),
            'phone'       => $request->input('phone'),
            'division_id' => $request->input('division'),
            'position'    => $request->input('position'),
        ];

        Employee::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Karyawan berhasil ditambahkan'
        ]);
    }

    public function update(Request $request, $id)
    {
        logger($request->all());
        $employee = Employee::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'phone'     => 'required|string|unique:employees,phone,' . $employee->id,
            'division'  => 'required|exists:divisions,id',
            'position'  => 'required|string|max:255',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'name'        => $request->input('name'),
            'phone'       => $request->input('phone'),
            'division_id' => $request->input('division'),
            'position'    => $request->input('position'),
        ];

        if ($request->hasFile('image')) {
            // Hapus gambar lama
            Storage::delete('employees/' . basename($employee->image));

            // Simpan gambar baru
            $image = $request->file('image');
            $image->storeAs('employees', $image->hashName());

            $data['image'] = $image->hashName();
        }

        $employee->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Karyawan berhasil diperbarui',
        ]);
    }

    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Karyawan tidak ditemukan',
            ], 404);
        }

        // Hapus gambar karyawan
        Storage::delete('employees/' . basename($employee->image));

        $employee->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Karyawan berhasil dihapus',
        ]);
    }
}
