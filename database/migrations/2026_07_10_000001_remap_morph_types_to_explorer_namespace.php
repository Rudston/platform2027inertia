<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Models moved from App\Models\... to App\Models\Explorer\... (User excepted).
 * Polymorphic *_type columns store the fully-qualified class name as a string,
 * so existing rows still point at the old namespace and must be remapped.
 *
 * Uses the query builder (bound values) rather than raw SQL REPLACE so the
 * literal backslashes in class names need no MySQL escaping. Exact-match
 * updates make this idempotent and safe to re-run.
 */
return new class extends Migration
{
    private const OLD_PREFIX = 'App\\Models\\';
    private const NEW_PREFIX = 'App\\Models\\Explorer\\';

    /** Polymorphic type columns that store moved model class names. */
    private array $targets = [
        ['circles', 'locatable_type'],
        ['circles', 'circleable_type'],
        ['requests', 'requestable_type'],
    ];

    public function up(): void
    {
        foreach ($this->targets as [$table, $column]) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
                continue;
            }

            $values = DB::table($table)->distinct()->pluck($column)->filter();

            foreach ($values as $value) {
                // Only remap classes that actually moved: under App\Models\,
                // not already migrated, and not the App\Models\User class.
                if (! str_starts_with($value, self::OLD_PREFIX)
                    || str_starts_with($value, self::NEW_PREFIX)
                    || $value === self::OLD_PREFIX . 'User') {
                    continue;
                }

                $new = self::NEW_PREFIX . substr($value, strlen(self::OLD_PREFIX));

                DB::table($table)->where($column, $value)->update([$column => $new]);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->targets as [$table, $column]) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
                continue;
            }

            $values = DB::table($table)->distinct()->pluck($column)->filter();

            foreach ($values as $value) {
                if (! str_starts_with($value, self::NEW_PREFIX)) {
                    continue;
                }

                $old = self::OLD_PREFIX . substr($value, strlen(self::NEW_PREFIX));

                DB::table($table)->where($column, $value)->update([$column => $old]);
            }
        }
    }
};
