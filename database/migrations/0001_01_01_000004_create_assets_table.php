<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::create( 'assets', function ( Blueprint $table ) {
			$table->id();
			$table->string( 'reference' );
			$table->string( 'serial_number' )->unique();
			$table->text( 'description' )->nullable();
			$table->foreignId( 'current_owner_id' )
			      ->nullable()
			      ->constrained( 'owners' )
			      ->nullOnDelete();
			$table->dateTime( 'current_owned_from' )->nullable();

			$table->index( 'reference' );

			$table->timestamps();
		} );
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::dropIfExists( 'assets' );
	}
};
