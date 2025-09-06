<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asset_owner_assignments', function (Blueprint $table) {
            $table->id();
			$table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
			$table->foreignId('owner_id')->constrained('owners')->cascadeOnDelete();
			$table->dateTime('owned_from');
			$table->dateTime('owned_to')->nullable();
            $table->timestamps();

			$table->index(['asset_id', 'owner_id']);
        });

	    // Partial unique: A single assignment opened per asset
	    DB::statement('CREATE UNIQUE INDEX asset_open_assignment_unique
                   ON asset_owner_assignments(asset_id)
                   WHERE owned_to IS NULL');
		// Avoid to have period for assignment in reverse
	    DB::statement('ALTER TABLE asset_owner_assignments
		  ADD CONSTRAINT owned_to_after_from
		  CHECK (owned_to IS NULL OR owned_to >= owned_from)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
	    DB::statement('DROP INDEX IF EXISTS asset_open_assignment_unique');
        Schema::dropIfExists('asset_owner_assignments');
    }
};
