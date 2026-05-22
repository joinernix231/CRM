<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('contacts')) {
            return;
        }

        DB::table('contacts')
            ->select('client_id')
            ->where('is_primary', true)
            ->groupBy('client_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('client_id')
            ->each(function (int $clientId) {
                $primaryId = DB::table('contacts')
                    ->where('client_id', $clientId)
                    ->where('is_primary', true)
                    ->orderBy('id')
                    ->value('id');

                DB::table('contacts')
                    ->where('client_id', $clientId)
                    ->where('is_primary', true)
                    ->where('id', '!=', $primaryId)
                    ->update(['is_primary' => false]);
            });

        if (Schema::hasColumn('contacts', 'primary_client_key')) {
            return;
        }

        try {
            DB::statement(
                'ALTER TABLE contacts
                 ADD COLUMN primary_client_key BIGINT UNSIGNED NULL
                 GENERATED ALWAYS AS (CASE WHEN is_primary = 1 THEN client_id ELSE NULL END) STORED'
            );

            DB::statement(
                'CREATE UNIQUE INDEX contacts_one_primary_per_client ON contacts (primary_client_key)'
            );
        } catch (\Throwable) {
            // Application-level enforcement remains active if this MySQL feature is unavailable.
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('contacts')) {
            return;
        }

        try {
            if (Schema::hasColumn('contacts', 'primary_client_key')) {
                DB::statement('ALTER TABLE contacts DROP INDEX contacts_one_primary_per_client');
                DB::statement('ALTER TABLE contacts DROP COLUMN primary_client_key');
            }
        } catch (\Throwable) {
            //
        }
    }
};
