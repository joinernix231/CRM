<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('client_id')
                ->constrained('users')
                ->cascadeOnDelete();
        });

        DB::table('contacts')
            ->join('clients', 'contacts.client_id', '=', 'clients.id')
            ->update(['contacts.user_id' => DB::raw('clients.user_id')]);

        DB::statement('ALTER TABLE contacts MODIFY user_id BIGINT UNSIGNED NOT NULL');
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
