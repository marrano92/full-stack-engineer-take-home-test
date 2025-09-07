<?php

declare(strict_types=1);

namespace App\Actions\Owners;

use App\Models\Owner;

class FindOrCreateOwnerAction
{
    public function __invoke(string $fullName): array
    {
        $fullName = trim($fullName);
        
        if (empty($fullName)) {
            throw new \InvalidArgumentException('Owner name cannot be empty');
        }

        $nameParts = explode(' ', $fullName, 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        $owner = Owner::whereRaw("CONCAT(first_name, ' ', last_name) = ?", [$fullName])->first();

        if (!$owner) {
            $owner = Owner::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]);
        }

        return [
            'id' => $owner->id,
            'name' => $owner->first_name . ' ' . $owner->last_name,
            'first_name' => $owner->first_name,
            'last_name' => $owner->last_name,
        ];
    }
}