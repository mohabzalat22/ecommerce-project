<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(): void
    {
        $schema = Capsule::schema();

        if (!$schema->hasTable('settings')) {
            $schema->create('settings', function (Blueprint $table): void {
                $table->id();
                $table->string('key')->unique();
                $table->string('value', 255)->nullable();
            });
        }

        Capsule::table('settings')->updateOrInsert(
            ['key' => 'prices.tax_enabled'],
            ['value' => '0'],
        );
    }

    public function down(): void
    {
        $schema = Capsule::schema();

        if ($schema->hasTable('settings')) {
            $schema->drop('settings');
        }
    }
};