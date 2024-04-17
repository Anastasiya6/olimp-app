<?php

namespace App\Services\GroupMt;

use App\Models\GroupMaterial;
use Illuminate\Http\Request;

class GroupMaterialService
{
    public function store(Request $request)
    {
        GroupMaterial::create([
            'material_id' => $request->material_id,
            'material_entry_id' => $request->material_entry_id,
            'norm' => $request->norm

        ]);
    }

    public function update(Request $request, GroupMaterial $groupMt)
    {
        $groupMt->norm = $request->norm;

        $groupMt->save();
    }
}
