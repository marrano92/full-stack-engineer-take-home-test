<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssetsController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Assets/Index', [
            // ... parameters if any ...
        ]);
    }

    public function add(Request $request): Response
    {
        return Inertia::render('Assets/Add', [
            // ... parameters if any ...
        ]);
    }

    public function edit(Request $request, Asset $asset): Response
    {
        return Inertia::render('Assets/Edit', [
            'asset' => [
                'id' => 1,
                'field1' => 'test',
            ],
        ]);
    }
}
