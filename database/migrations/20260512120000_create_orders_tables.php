<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(): void
    {
        $schema = Capsule::schema();

        if (!$schema->hasTable('orders')) {
            $schema->create('orders', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->string('email', 255);
                $table->string('full_name', 255);
                $table->string('address_line1', 512);
                $table->string('city', 255);
                $table->string('postal_code', 32);
                $table->unsignedInteger('subtotal_cents');
                $table->unsignedInteger('shipping_cents');
                $table->unsignedInteger('total_cents');
                $table->timestamps();
            });
        }

        if (!$schema->hasTable('order_items')) {
            $schema->create('order_items', function (Blueprint $table): void {
                $table->id();
                $table->uuid('order_id');
                $table->unsignedBigInteger('product_id');
                $table->string('name', 512);
                $table->string('image_url', 2048);
                $table->unsignedInteger('unit_price_cents');
                $table->unsignedInteger('quantity');
                $table->string('size_label', 128);
                $table->string('color_label', 128);
                $table->unsignedInteger('line_total_cents');

                $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
                $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
            });
        }
    }

    public function down(): void
    {
        $schema = Capsule::schema();

        if ($schema->hasTable('order_items')) {
            $schema->drop('order_items');
        }

        if ($schema->hasTable('orders')) {
            $schema->drop('orders');
        }
    }
};
