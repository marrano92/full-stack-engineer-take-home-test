<?php
declare(strict_types=1);


namespace App\Actions\Assets;

use App\Models\Asset;
use App\Models\AssetOwnerAssignment;

class AssignOwnerResultDTO
{
    public function __construct(
        public bool $changed,
        public Asset $asset,
        public ?AssetOwnerAssignment $newAssignment = null,
        public ?string $reason = null
    ) {
    }
}
