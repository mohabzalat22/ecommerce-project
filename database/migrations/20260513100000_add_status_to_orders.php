<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(): void
    {
        $schema = Capsule::schema();

        if ($schema->hasTable('orders') && !$schema->hasColumn('orders', 'status')) {
            $schema->table('orders', function (Blueprint $table): void {
                $table->string('status', 32)->default('pending')->after('total_cents');
            });
        }
    }

    public function down(): void
    {
        $schema = Capsule::schema();

        if ($schema->hasTable('orders') && $schema->hasColumn('orders', 'status')) {
            $schema->table('orders', function (Blueprint $table): void {
                $table->dropColumn('status');
            });
        }
    }
};
