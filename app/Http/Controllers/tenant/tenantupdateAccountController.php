<?php

namespace App\Http\Controllers\tenant;

use App\Http\Controllers\Controller;
use App\Models\tenant\tenantModel;
use Illuminate\Http\Request;

class tenantupdateAccountController extends Controller
{
    public function updateAccount(Request $request, $id)
    {
        $tenant = tenantModel::find($id);

        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }

        $tenant->update($request->all());

        return response()->json(['message' => 'Account updated successfully']);
    }
}
