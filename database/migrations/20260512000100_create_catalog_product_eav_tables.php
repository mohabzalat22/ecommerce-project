<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(): void
    {
        $schema = Capsule::schema();

        if (!$schema->hasTable('categories')) {
            $schema->create('categories', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('image_url', 2048)->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('parent_id')->references('id')->on('categories')->nullOnDelete();
            });
        }

        if (!$schema->hasTable('products')) {
            $schema->create('products', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('category_id');
                $table->string('name', 512);
                $table->string('slug', 512)->unique();
                $table->string('sku', 100)->unique();
                $table->decimal('base_price', 12, 2)->default(0.00);
                $table->decimal('sale_price', 12, 2)->nullable();
                $table->integer('stock_qty')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete();
            });
        }

        if (!$schema->hasTable('product_images')) {
            $schema->create('product_images', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->string('image_url', 2048);
                $table->string('alt_text', 512)->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_primary')->default(false);

                $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            });
        }

        if (!$schema->hasTable('eav_attributes')) {
            $schema->create('eav_attributes', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('type', 20)->default('varchar');
                $table->boolean('is_required')->default(false);
                $table->boolean('is_filterable')->default(false);
                $table->boolean('is_searchable')->default(false);
                $table->integer('sort_order')->default(0);
            });
        }

        if (!$schema->hasTable('eav_attribute_options')) {
            $schema->create('eav_attribute_options', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('attribute_id');
                $table->string('label');
                $table->string('value');
                $table->integer('sort_order')->default(0);

                $table->foreign('attribute_id')->references('id')->on('eav_attributes')->cascadeOnDelete();
            });
        }

        if (!$schema->hasTable('eav_product_values')) {
            $schema->create('eav_product_values', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('attribute_id');
                $table->unsignedBigInteger('option_id')->nullable();
                $table->string('value_varchar', 512)->nullable();
                $table->text('value_text')->nullable();
                $table->integer('value_int')->nullable();
                $table->decimal('value_decimal', 12, 4)->nullable();
                $table->timestamp('value_datetime')->nullable();

                $table->unique(['product_id', 'attribute_id']);

                $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
                $table->foreign('attribute_id')->references('id')->on('eav_attributes')->cascadeOnDelete();
                $table->foreign('option_id')->references('id')->on('eav_attribute_options')->nullOnDelete();
            });
        }

        if (!$schema->hasTable('category_attributes')) {
            $schema->create('category_attributes', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('category_id');
                $table->unsignedBigInteger('attribute_id');
                $table->integer('sort_order')->default(0);

                $table->unique(['category_id', 'attribute_id']);

                $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
                $table->foreign('attribute_id')->references('id')->on('eav_attributes')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        $schema = Capsule::schema();

        if ($schema->hasTable('category_attributes')) {
            $schema->drop('category_attributes');
        }

        if ($schema->hasTable('eav_product_values')) {
            $schema->drop('eav_product_values');
        }

        if ($schema->hasTable('eav_attribute_options')) {
            $schema->drop('eav_attribute_options');
        }

        if ($schema->hasTable('eav_attributes')) {
            $schema->drop('eav_attributes');
        }

        if ($schema->hasTable('product_images')) {
            $schema->drop('product_images');
        }

        if ($schema->hasTable('products')) {
            $schema->drop('products');
        }

        if ($schema->hasTable('categories')) {
            $schema->drop('categories');
        }
    }
};
