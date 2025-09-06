<?php
declare(strict_types=1);


namespace App\Actions\Assets;

use App\Models\Asset;
use App\Models\AssetOwnerAssignment;
use App\Models\Owner;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class ActionAssignOwnerToAsset
{
    public function __invoke(
        Asset $asset,
        ?Owner $newOwner,
        ?CarbonInterface $effectiveAt = null,
        ?int $changedByUserId = null
    ): AssignOwnerResultDTO {
        $at = $effectiveAt?->utc() ?? now()->utc();

        return DB::transaction(function () use ($asset, $newOwner, $at, $changedByUserId) {
            $asset = Asset::query()
                          ->whereKey($asset->getKey())
                          ->lockForUpdate()
                          ->firstOrFail();

            if ($newOwner === null) {
                $asset->forceFill([
                    'current_owner_id'   => null,
                    'current_owned_from' => null,
                ])->save();

                return new AssignOwnerResultDTO(
                    changed: true,
                    asset: $asset->fresh('owner'),
                    newAssignment: null,
                    reason: 'removed_owner'
                );
            }

            $currentId = $asset->current_owner_id;
            $newId     = $newOwner->id;

            if ($currentId === $newId) {
                return new AssignOwnerResultDTO(
                    changed: false,
                    asset: $asset->fresh('owner'),
                    newAssignment: null,
                    reason: 'no_change'
                );
            }

            $open = AssetOwnerAssignment::open()->forAsset($asset->id)->first();

            if ($open) {
                $open->owned_to = $at;
                $open->save();
            }

            $assignment = AssetOwnerAssignment::create([
                'asset_id'   => $asset->id,
                'owner_id'   => $newOwner->id,
                'owned_from' => $at,
                'owned_to'   => null,
            ]);

            $asset->forceFill([
                'current_owner_id'   => $newOwner->id,
                'current_owned_from' => $at,
            ])->save();

            return new AssignOwnerResultDTO(
                changed: true,
                asset: $asset->fresh('owner'),
                newAssignment: $assignment,
                reason: 'assigned'
            );
        });
    }
}
