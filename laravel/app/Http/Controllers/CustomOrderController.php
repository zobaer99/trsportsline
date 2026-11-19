<?php

namespace App\Http\Controllers;

use App\Models\CustomOrderInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomOrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['team_name' => ['required','string','max:120'],'contact_name' => ['required','string','max:120'],'phone' => ['required','regex:/^01[3-9][0-9]{8}$/'],'quantity' => ['required','integer','min:10','max:10000'],'fabric' => ['required','in:Honeycomb Mesh,Jacquard,PP'],'district' => ['required','string','max:80'],'notes' => ['nullable','string','max:4000'],'design_file' => ['nullable','file','mimes:pdf,png,jpg,jpeg,svg','max:10240']]);
        $path = $request->file('design_file')?->store('custom-orders', 'public');
        $inquiry = CustomOrderInquiry::create([...$data, 'design_file_path' => $path]);
        return response()->json(['message' => 'Your custom kit request was received.','data' => $inquiry], 201);
    }
}
