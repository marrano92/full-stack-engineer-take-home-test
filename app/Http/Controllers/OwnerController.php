<?php

namespace App\Http\Controllers;

use App\Actions\Owners\FindOrCreateOwnerAction;
use App\Actions\Owners\SearchOwnersAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function search(Request $request, SearchOwnersAction $searchAction): JsonResponse
    {
        $query  = $request->get('q', '');
        $owners = $searchAction($query);

        return response()->json($owners);
    }

    public function findOrCreate(Request $request, FindOrCreateOwnerAction $findOrCreateAction): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $owner = $findOrCreateAction($request->name);

            return response()->json($owner);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
