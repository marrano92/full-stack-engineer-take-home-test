<?php

namespace App\Http\Controllers;

use App\Actions\Assets\ActionAssignOwnerToAsset;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\Owner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class AssetController extends Controller
{
    public function index(): Response
    {
        $assets = Asset::with('owner')->latest()->paginate(10);

        return Inertia::render('Assets/Index', [
            'assets' => $assets
        ]);
    }

    public function store(
        StoreAssetRequest $request,
        ActionAssignOwnerToAsset $actionAssignOwnerToAsset
    ): RedirectResponse {
        $data  = $request->validated();
        $asset = Asset::create([
            'reference'     => $data['reference'],
            'serial_number' => $data['serial_number'],
            'description'   => $data['description'] ?? null,
        ]);

        $owner          = Owner::find($data['owner_id']) ?? null;
        $assignmentDate = Carbon::parse($data['owned_from'], config('app.timezone')) ?? now();

        $actionAssignOwnerToAsset($asset, $owner, $assignmentDate, auth()->id());

        return redirect()->route('assets.index')->with('success', 'Asset created successfully.');
    }

    public function update(
        UpdateAssetRequest $request,
        Asset $asset,
        ActionAssignOwnerToAsset $actionAssignOwnerToAsset
    ): RedirectResponse {
        $data = $request->validated();
        $asset->update([
            'reference'     => $data['reference'],
            'serial_number' => $data['serial_number'],
            'description'   => $data['description'] ?? null,
        ]);

        $owner          = Owner::find($data['owner_id']) ?? null;
        $assignmentDate = Carbon::parse($data['owned_from'], config('app.timezone')) ?? now();

        $actionAssignOwnerToAsset($asset, $owner, $assignmentDate, auth()->id());

        return redirect()->route('assets.index')->with('success', 'Asset updated!');
    }

    public function pageNew(): Response
    {
        $owners = Owner::orderBy('last_name')->limit(200)->get(['id', 'first_name', 'last_name']);

        return Inertia::render('Assets/Create', [
            'owners' => $owners
        ]);
    }

    public function pageEdit(Asset $asset): Response
    {
        $asset->load('owner');
        $owners  = Owner::orderBy('last_name')->limit(200)->get(['id', 'first_name', 'last_name']);
        $history = $asset->assignments()->with('owner')->paginate(10);

        return Inertia::render('Assets/Edit', [
            'asset'   => $asset,
            'owners'  => $owners,
            'history' => $history,
        ]);
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        $asset->delete();

        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully');
    }
}
