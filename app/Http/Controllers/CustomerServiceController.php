<?php

namespace App\Http\Controllers;

use App\Models\CustomerService;
use Illuminate\Http\Request;

class CustomerServiceController extends Controller
{
    public function getCustomerService () 
    {
        return response()->json([
            'success' => true,
            'results' => CustomerService::all()
        ]);
    }
    public function index () 
    {
        return response()->json([
            'success' => true,
            'results' => CustomerService::all()
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'phone' => ['required', 'string', 'max:20', 'unique:customer_services'],
        ],[
            'name.required' => 'Nama wajib diisi.',
            'phone.required' => 'Nomor ponsel wajib diisi.',
            'phone.unique' => 'Nomor ponsel sudah terdaftar.',
        ]);

        $cs = CustomerService::create([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return response([
            'success' => true,
            'results' => $cs
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $cs = CustomerService::find($id);

        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:customer_services,phone,' .$id,
        ],[
            'name.required' => 'Nama wajib diisi.',
            'phone.required' => 'Nomor ponsel wajib diisi.',
            'phone.unique' => 'Nomor ponsel sudah terdaftar.',
        ]);


        $cs->name = $request->name;
        $cs->phone = $request->phone;

        $cs->save();

        return response([
            'success' => true,
            'results' => $cs
        ], 200);


    }
    public function destroy($id)
    {
        CustomerService::where('id', $id)->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
