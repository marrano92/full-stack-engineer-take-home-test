<?php

declare(strict_types=1);

namespace App\Actions\Owners;

use App\Models\Owner;
use Illuminate\Support\Collection;

class SearchOwnersAction
{
    public function __invoke(string $query): Collection
    {
        if (empty(trim($query))) {
            return collect([]);
        }

        $searchQuery = trim($query);

        return Owner::where('first_name', 'LIKE', "%{$searchQuery}%")
                   ->orWhere('last_name', 'LIKE', "%{$searchQuery}%")
                   ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchQuery}%"])
                   ->limit(10)
                   ->get()
                   ->map(function (Owner $owner) {
                       return [
                           'id'         => $owner->id,
                           'name'       => $owner->first_name . ' ' . $owner->last_name,
                           'first_name' => $owner->first_name,
                           'last_name'  => $owner->last_name,
                       ];
                   });
    }
}