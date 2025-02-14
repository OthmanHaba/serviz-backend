<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServicTypeResource;
use App\Models\ServicType;

class ServicController extends Controller
{
    public function index()
    {
        $servces = ServicType::all();

        return ServicTypeResource::collection($servces);
    }

    public function show($id)
    {
        $servces = ServicType::find($id);

        return new ServicTypeResource($servces);
    }
}
