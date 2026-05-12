<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(): void
    {
        $schema = Capsule::schema();

        if (!$schema->hasTable('users')) {
            return;
        }

        if (!$schema->hasColumn('users', 'role')) {
            $schema->table('users', function (Blueprint $table): void {
                $table->string('role', 32)->default('customer');
            });
        }
    }

    public function down(): void
    {
        $schema = Capsule::schema();

        if ($schema->hasTable('users') && $schema->hasColumn('users', 'role')) {
            $schema->table('users', function (Blueprint $table): void {
                $table->dropColumn('role');
            });
        }
    }
};
