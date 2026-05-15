<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;

class EcommerceSeeder
{
    public function run(): void
    {
        $this->seedCategories();
        $this->seedProducts();
        $this->seedProductImages();
        $this->seedEavAttributes();
        $this->seedEavAttributeOptions();
        $this->seedCategoryAttributes();
        $this->seedEavProductValues();
    }

    private function seedCategories(): void
    {
        // Truncate table
        Capsule::table('categories')->truncate();

        $categories = [
            // Root
            [
                'id' => 1,
                'parent_id' => null,
                'name' => "Men's Clothing",
                'slug' => 'mens-clothing',
                'image_url' => $this->publicAssetUrl($this->categoryImageFilename(1)),
                'description' => "All men's apparel — new arrivals, essentials and classics.",
                'is_active' => true,
                'sort_order' => 0,
            ],
            // Level-2 (subcategories)
            [
                'id' => 2,
                'parent_id' => 1,
                'name' => 'Jackets & Outerwear',
                'slug' => 'jackets-outerwear',
                'image_url' => $this->publicAssetUrl($this->categoryImageFilename(2)),
                'description' => 'Overshirts, field jackets, bombers and coats.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'id' => 3,
                'parent_id' => 1,
                'name' => 'Shirts & Tops',
                'slug' => 'shirts-tops',
                'image_url' => $this->publicAssetUrl($this->categoryImageFilename(3)),
                'description' => 'Oxford shirts, flannels, henleys and tees.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'id' => 4,
                'parent_id' => 1,
                'name' => 'Pants & Chinos',
                'slug' => 'pants-chinos',
                'image_url' => $this->publicAssetUrl($this->categoryImageFilename(4)),
                'description' => 'Slim, regular and relaxed fit trousers.',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'id' => 5,
                'parent_id' => 1,
                'name' => 'Jeans',
                'slug' => 'jeans',
                'image_url' => $this->publicAssetUrl($this->categoryImageFilename(5)),
                'description' => 'Raw, washed and distressed denim.',
                'is_active' => true,
                'sort_order' => 4,
            ],
            // Level-3 (sub-subcategories)
            [
                'id' => 6,
                'parent_id' => 2,
                'name' => 'Overshirts',
                'slug' => 'overshirts',
                'image_url' => $this->publicAssetUrl($this->categoryImageFilename(6)),
                'description' => 'Wear-over tees or layer under a coat.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'id' => 7,
                'parent_id' => 2,
                'name' => 'Field Jackets',
                'slug' => 'field-jackets',
                'image_url' => $this->publicAssetUrl($this->categoryImageFilename(7)),
                'description' => 'Utility-inspired, weather-ready outerwear.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'id' => 8,
                'parent_id' => 3,
                'name' => 'Flannel Shirts',
                'slug' => 'flannel-shirts',
                'image_url' => $this->publicAssetUrl($this->categoryImageFilename(8)),
                'description' => 'Brushed cotton flannel in seasonal plaids.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'id' => 9,
                'parent_id' => 3,
                'name' => 'Oxford Shirts',
                'slug' => 'oxford-shirts',
                'image_url' => $this->publicAssetUrl($this->categoryImageFilename(9)),
                'description' => 'Classic button-down Oxford weave.',
                'is_active' => true,
                'sort_order' => 2,
            ],
        ];

        Capsule::table('categories')->insert($categories);
    }

    private function seedProducts(): void
    {
        Capsule::table('products')->truncate();

        $products = [
            [
                'id' => 1,
                'category_id' => 6,
                'name' => 'Highland Plaid Overshirt',
                'slug' => 'highland-plaid-overshirt',
                'sku' => 'OVS-001',
                'base_price' => 129.00,
                'sale_price' => null,
                'stock_qty' => 45,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'category_id' => 6,
                'name' => 'Essential Black Overshirt',
                'slug' => 'essential-black-overshirt',
                'sku' => 'OVS-002',
                'base_price' => 99.00,
                'sale_price' => 79.00,
                'stock_qty' => 32,
                'is_active' => true,
            ],
            [
                'id' => 3,
                'category_id' => 7,
                'name' => 'Olive Field Jacket',
                'slug' => 'olive-field-jacket',
                'sku' => 'FJK-001',
                'base_price' => 179.00,
                'sale_price' => null,
                'stock_qty' => 28,
                'is_active' => true,
            ],
            [
                'id' => 4,
                'category_id' => 7,
                'name' => 'Midnight Utility Jacket',
                'slug' => 'midnight-utility-jacket',
                'sku' => 'FJK-002',
                'base_price' => 159.00,
                'sale_price' => 139.00,
                'stock_qty' => 19,
                'is_active' => true,
            ],
            [
                'id' => 5,
                'category_id' => 9,
                'name' => 'Classic White Oxford Shirt',
                'slug' => 'classic-white-oxford-shirt',
                'sku' => 'OXF-001',
                'base_price' => 79.00,
                'sale_price' => null,
                'stock_qty' => 60,
                'is_active' => true,
            ],
            [
                'id' => 6,
                'category_id' => 5,
                'name' => 'Slim Indigo Jeans',
                'slug' => 'slim-indigo-jeans',
                'sku' => 'JNS-001',
                'base_price' => 119.00,
                'sale_price' => null,
                'stock_qty' => 50,
                'is_active' => true,
            ],
            [
                'id' => 7,
                'category_id' => 4,
                'name' => 'Tapered Grey Chinos',
                'slug' => 'tapered-grey-chinos',
                'sku' => 'CHN-001',
                'base_price' => 89.00,
                'sale_price' => 69.00,
                'stock_qty' => 35,
                'is_active' => true,
            ],
        ];

        Capsule::table('products')->insert($products);
    }

    private function seedProductImages(): void
    {
        Capsule::table('product_images')->truncate();

        $images = [
            // Product 1 — Highland Plaid Overshirt
            [
                'id' => 1,
                'product_id' => 1,
                'image_url' => $this->publicAssetUrl($this->productImageFilename(1, 0)),
                'alt_text' => 'Highland Plaid Overshirt — front',
                'sort_order' => 0,
                'is_primary' => true,
            ],
            [
                'id' => 2,
                'product_id' => 1,
                'image_url' => $this->publicAssetUrl($this->productImageFilename(1, 1)),
                'alt_text' => 'Highland Plaid Overshirt — detail',
                'sort_order' => 1,
                'is_primary' => false,
            ],
            [
                'id' => 3,
                'product_id' => 1,
                'image_url' => $this->publicAssetUrl($this->productImageFilename(1, 2)),
                'alt_text' => 'Highland Plaid Overshirt — back',
                'sort_order' => 2,
                'is_primary' => false,
            ],
            // Product 2 — Essential Black Overshirt
            [
                'id' => 4,
                'product_id' => 2,
                'image_url' => $this->publicAssetUrl($this->productImageFilename(2, 0)),
                'alt_text' => 'Essential Black Overshirt — front',
                'sort_order' => 0,
                'is_primary' => true,
            ],
            [
                'id' => 5,
                'product_id' => 2,
                'image_url' => $this->publicAssetUrl($this->productImageFilename(2, 1)),
                'alt_text' => 'Essential Black Overshirt — back',
                'sort_order' => 1,
                'is_primary' => false,
            ],
            // Product 3 — Olive Field Jacket
            [
                'id' => 6,
                'product_id' => 3,
                'image_url' => $this->publicAssetUrl($this->productImageFilename(3, 0)),
                'alt_text' => 'Olive Field Jacket — front',
                'sort_order' => 0,
                'is_primary' => true,
            ],
            [
                'id' => 7,
                'product_id' => 3,
                'image_url' => $this->publicAssetUrl($this->productImageFilename(3, 1)),
                'alt_text' => 'Olive Field Jacket — side',
                'sort_order' => 1,
                'is_primary' => false,
            ],
            // Product 4 — Midnight Utility Jacket
            [
                'id' => 8,
                'product_id' => 4,
                'image_url' => $this->publicAssetUrl($this->productImageFilename(4, 0)),
                'alt_text' => 'Midnight Utility Jacket — front',
                'sort_order' => 0,
                'is_primary' => true,
            ],
            // Product 5 — Classic White Oxford Shirt
            [
                'id' => 9,
                'product_id' => 5,
                'image_url' => $this->publicAssetUrl($this->productImageFilename(5, 0)),
                'alt_text' => 'Classic White Oxford Shirt — front',
                'sort_order' => 0,
                'is_primary' => true,
            ],
            [
                'id' => 10,
                'product_id' => 5,
                'image_url' => $this->publicAssetUrl($this->productImageFilename(5, 1)),
                'alt_text' => 'Classic White Oxford Shirt — tucked',
                'sort_order' => 1,
                'is_primary' => false,
            ],
            // Product 6 — Slim Indigo Jeans
            [
                'id' => 11,
                'product_id' => 6,
                'image_url' => $this->publicAssetUrl($this->productImageFilename(6, 0)),
                'alt_text' => 'Slim Indigo Jeans — front',
                'sort_order' => 0,
                'is_primary' => true,
            ],
            // Product 7 — Tapered Grey Chinos
            [
                'id' => 12,
                'product_id' => 7,
                'image_url' => $this->publicAssetUrl($this->productImageFilename(7, 0)),
                'alt_text' => 'Tapered Grey Chinos — front',
                'sort_order' => 0,
                'is_primary' => true,
            ],
        ];

        Capsule::table('product_images')->insert($images);
    }

    /**
     * @return list<string>
     */
    private function publicAssetImageFiles(): array
    {
        return ['2.png', '3.png', '4.png', '5.png', '6.png', '7.png'];
    }

    private function publicAssetUrl(string $filename): string
    {
        return 'http://localhost:8000/assets/'.$filename;
    }

    private function productImageFilename(int $productId, int $sortOrder): string
    {
        $files = $this->publicAssetImageFiles();

        return $files[(($productId - 1) + $sortOrder) % count($files)];
    }

    /** Maps each category to a PNG under public/assets (1.png–7.png). */
    private function categoryImageFilename(int $categoryId): string
    {
        return match ($categoryId) {
            1 => '1.png',
            2 => '2.png',
            3 => '3.png',
            4 => '4.png',
            5 => '5.png',
            6 => '6.png',
            7 => '7.png',
            8 => '2.png',
            9 => '3.png',
            default => '1.png',
        };
    }

    private function seedEavAttributes(): void
    {
        Capsule::table('eav_attributes')->truncate();

        $attributes = [
            [
                'id' => 1,
                'name' => 'Color',
                'code' => 'color',
                'type' => 'select',
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Size',
                'code' => 'size',
                'type' => 'select',
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => true,
                'sort_order' => 2,
            ],
            [
                'id' => 3,
                'name' => 'Material',
                'code' => 'material',
                'type' => 'varchar',
                'is_required' => true,
                'is_filterable' => false,
                'is_searchable' => true,
                'sort_order' => 3,
            ],
            [
                'id' => 4,
                'name' => 'Fit',
                'code' => 'fit',
                'type' => 'select',
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 4,
            ],
            [
                'id' => 5,
                'name' => 'Collar Style',
                'code' => 'collar_style',
                'type' => 'select',
                'is_required' => false,
                'is_filterable' => false,
                'is_searchable' => false,
                'sort_order' => 5,
            ],
            [
                'id' => 6,
                'name' => 'Waist (in)',
                'code' => 'waist',
                'type' => 'select',
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 6,
            ],
            [
                'id' => 7,
                'name' => 'Inseam (in)',
                'code' => 'inseam',
                'type' => 'select',
                'is_required' => true,
                'is_filterable' => true,
                'is_searchable' => false,
                'sort_order' => 7,
            ],
            [
                'id' => 8,
                'name' => 'Weight (gsm)',
                'code' => 'weight_gsm',
                'type' => 'int',
                'is_required' => false,
                'is_filterable' => false,
                'is_searchable' => false,
                'sort_order' => 8,
            ],
            [
                'id' => 9,
                'name' => 'Description',
                'code' => 'description',
                'type' => 'text',
                'is_required' => true,
                'is_filterable' => false,
                'is_searchable' => true,
                'sort_order' => 9,
            ],
            [
                'id' => 10,
                'name' => 'Care Instructions',
                'code' => 'care',
                'type' => 'text',
                'is_required' => false,
                'is_filterable' => false,
                'is_searchable' => false,
                'sort_order' => 10,
            ],
        ];

        Capsule::table('eav_attributes')->insert($attributes);
    }

    private function seedEavAttributeOptions(): void
    {
        Capsule::table('eav_attribute_options')->truncate();

        $options = [
            // Color (attr 1)
            ['id' => 1, 'attribute_id' => 1, 'label' => 'Black', 'value' => 'black', 'sort_order' => 1],
            ['id' => 2, 'attribute_id' => 1, 'label' => 'White', 'value' => 'white', 'sort_order' => 2],
            ['id' => 3, 'attribute_id' => 1, 'label' => 'Olive', 'value' => 'olive', 'sort_order' => 3],
            ['id' => 4, 'attribute_id' => 1, 'label' => 'Navy', 'value' => 'navy', 'sort_order' => 4],
            ['id' => 5, 'attribute_id' => 1, 'label' => 'Brown Plaid', 'value' => 'brown-plaid', 'sort_order' => 5],
            ['id' => 6, 'attribute_id' => 1, 'label' => 'Grey', 'value' => 'grey', 'sort_order' => 6],
            ['id' => 7, 'attribute_id' => 1, 'label' => 'Indigo', 'value' => 'indigo', 'sort_order' => 7],
            ['id' => 8, 'attribute_id' => 1, 'label' => 'Khaki', 'value' => 'khaki', 'sort_order' => 8],

            // Size — apparel (attr 2)
            ['id' => 9, 'attribute_id' => 2, 'label' => 'XS', 'value' => 'xs', 'sort_order' => 1],
            ['id' => 10, 'attribute_id' => 2, 'label' => 'S', 'value' => 's', 'sort_order' => 2],
            ['id' => 11, 'attribute_id' => 2, 'label' => 'M', 'value' => 'm', 'sort_order' => 3],
            ['id' => 12, 'attribute_id' => 2, 'label' => 'L', 'value' => 'l', 'sort_order' => 4],
            ['id' => 13, 'attribute_id' => 2, 'label' => 'XL', 'value' => 'xl', 'sort_order' => 5],
            ['id' => 14, 'attribute_id' => 2, 'label' => 'XXL', 'value' => 'xxl', 'sort_order' => 6],

            // Fit (attr 4)
            ['id' => 15, 'attribute_id' => 4, 'label' => 'Slim', 'value' => 'slim', 'sort_order' => 1],
            ['id' => 16, 'attribute_id' => 4, 'label' => 'Regular', 'value' => 'regular', 'sort_order' => 2],
            ['id' => 17, 'attribute_id' => 4, 'label' => 'Relaxed', 'value' => 'relaxed', 'sort_order' => 3],

            // Collar Style (attr 5)
            ['id' => 18, 'attribute_id' => 5, 'label' => 'Button-Down', 'value' => 'button-down', 'sort_order' => 1],
            ['id' => 19, 'attribute_id' => 5, 'label' => 'Spread', 'value' => 'spread', 'sort_order' => 2],
            ['id' => 20, 'attribute_id' => 5, 'label' => 'Band', 'value' => 'band', 'sort_order' => 3],
            ['id' => 21, 'attribute_id' => 5, 'label' => 'None', 'value' => 'none', 'sort_order' => 4],

            // Waist in inches (attr 6)
            ['id' => 22, 'attribute_id' => 6, 'label' => '28', 'value' => '28', 'sort_order' => 1],
            ['id' => 23, 'attribute_id' => 6, 'label' => '30', 'value' => '30', 'sort_order' => 2],
            ['id' => 24, 'attribute_id' => 6, 'label' => '32', 'value' => '32', 'sort_order' => 3],
            ['id' => 25, 'attribute_id' => 6, 'label' => '34', 'value' => '34', 'sort_order' => 4],
            ['id' => 26, 'attribute_id' => 6, 'label' => '36', 'value' => '36', 'sort_order' => 5],

            // Inseam in inches (attr 7)
            ['id' => 27, 'attribute_id' => 7, 'label' => '28', 'value' => '28', 'sort_order' => 1],
            ['id' => 28, 'attribute_id' => 7, 'label' => '30', 'value' => '30', 'sort_order' => 2],
            ['id' => 29, 'attribute_id' => 7, 'label' => '32', 'value' => '32', 'sort_order' => 3],
        ];

        Capsule::table('eav_attribute_options')->insert($options);
    }

    private function seedCategoryAttributes(): void
    {
        Capsule::table('category_attributes')->truncate();

        $categoryAttributes = [
            // Men's Clothing (root) — global attributes
            ['id' => 1, 'category_id' => 1, 'attribute_id' => 1, 'sort_order' => 1],
            ['id' => 2, 'category_id' => 1, 'attribute_id' => 2, 'sort_order' => 2],
            ['id' => 3, 'category_id' => 1, 'attribute_id' => 3, 'sort_order' => 3],
            ['id' => 4, 'category_id' => 1, 'attribute_id' => 4, 'sort_order' => 4],
            ['id' => 5, 'category_id' => 1, 'attribute_id' => 9, 'sort_order' => 5],
            ['id' => 6, 'category_id' => 1, 'attribute_id' => 10, 'sort_order' => 6],

            // Jackets & Outerwear
            ['id' => 7, 'category_id' => 2, 'attribute_id' => 1, 'sort_order' => 1],
            ['id' => 8, 'category_id' => 2, 'attribute_id' => 2, 'sort_order' => 2],
            ['id' => 9, 'category_id' => 2, 'attribute_id' => 3, 'sort_order' => 3],
            ['id' => 10, 'category_id' => 2, 'attribute_id' => 4, 'sort_order' => 4],
            ['id' => 11, 'category_id' => 2, 'attribute_id' => 8, 'sort_order' => 5],
            ['id' => 12, 'category_id' => 2, 'attribute_id' => 9, 'sort_order' => 6],

            // Shirts & Tops
            ['id' => 13, 'category_id' => 3, 'attribute_id' => 1, 'sort_order' => 1],
            ['id' => 14, 'category_id' => 3, 'attribute_id' => 2, 'sort_order' => 2],
            ['id' => 15, 'category_id' => 3, 'attribute_id' => 3, 'sort_order' => 3],
            ['id' => 16, 'category_id' => 3, 'attribute_id' => 4, 'sort_order' => 4],
            ['id' => 17, 'category_id' => 3, 'attribute_id' => 5, 'sort_order' => 5],
            ['id' => 18, 'category_id' => 3, 'attribute_id' => 8, 'sort_order' => 6],
            ['id' => 19, 'category_id' => 3, 'attribute_id' => 9, 'sort_order' => 7],

            // Pants & Chinos
            ['id' => 20, 'category_id' => 4, 'attribute_id' => 1, 'sort_order' => 1],
            ['id' => 21, 'category_id' => 4, 'attribute_id' => 6, 'sort_order' => 2],
            ['id' => 22, 'category_id' => 4, 'attribute_id' => 7, 'sort_order' => 3],
            ['id' => 23, 'category_id' => 4, 'attribute_id' => 3, 'sort_order' => 4],
            ['id' => 24, 'category_id' => 4, 'attribute_id' => 4, 'sort_order' => 5],
            ['id' => 25, 'category_id' => 4, 'attribute_id' => 9, 'sort_order' => 6],

            // Jeans
            ['id' => 26, 'category_id' => 5, 'attribute_id' => 1, 'sort_order' => 1],
            ['id' => 27, 'category_id' => 5, 'attribute_id' => 6, 'sort_order' => 2],
            ['id' => 28, 'category_id' => 5, 'attribute_id' => 7, 'sort_order' => 3],
            ['id' => 29, 'category_id' => 5, 'attribute_id' => 4, 'sort_order' => 4],
            ['id' => 30, 'category_id' => 5, 'attribute_id' => 9, 'sort_order' => 5],

            // Overshirts
            ['id' => 31, 'category_id' => 6, 'attribute_id' => 1, 'sort_order' => 1],
            ['id' => 32, 'category_id' => 6, 'attribute_id' => 2, 'sort_order' => 2],
            ['id' => 33, 'category_id' => 6, 'attribute_id' => 3, 'sort_order' => 3],
            ['id' => 34, 'category_id' => 6, 'attribute_id' => 4, 'sort_order' => 4],
            ['id' => 35, 'category_id' => 6, 'attribute_id' => 5, 'sort_order' => 5],
            ['id' => 36, 'category_id' => 6, 'attribute_id' => 8, 'sort_order' => 6],
            ['id' => 37, 'category_id' => 6, 'attribute_id' => 9, 'sort_order' => 7],
        ];

        Capsule::table('category_attributes')->insert($categoryAttributes);
    }

    private function seedEavProductValues(): void
    {
        Capsule::table('eav_product_values')->truncate();

        $productValues = [
            // ── Product 1: Highland Plaid Overshirt ───────────────────────────
            [
                'product_id' => 1,
                'attribute_id' => 1,
                'option_id' => 5,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 1,
                'attribute_id' => 3,
                'option_id' => null,
                'value_varchar' => '100% Brushed Cotton Flannel',
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 1,
                'attribute_id' => 4,
                'option_id' => 16,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 1,
                'attribute_id' => 5,
                'option_id' => 18,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 1,
                'attribute_id' => 8,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => 280,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 1,
                'attribute_id' => 9,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => 'A heritage plaid overshirt cut from heavyweight brushed cotton flannel. Wear open over a tee or buttoned up against the morning chill. Two chest pockets, a relaxed body and curved hem.',
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 1,
                'attribute_id' => 10,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => 'Machine wash cold. Tumble dry low. Do not bleach.',
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],

            // ── Product 2: Essential Black Overshirt ─────────────────────────
            [
                'product_id' => 2,
                'attribute_id' => 1,
                'option_id' => 1,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 2,
                'attribute_id' => 3,
                'option_id' => null,
                'value_varchar' => '98% Cotton, 2% Elastane Twill',
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 2,
                'attribute_id' => 4,
                'option_id' => 15,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 2,
                'attribute_id' => 5,
                'option_id' => 21,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 2,
                'attribute_id' => 8,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => 240,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 2,
                'attribute_id' => 9,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => 'A wardrobe-staple overshirt in a clean black twill. Four-button placket, tonal stitching and a slightly cropped body.',
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 2,
                'attribute_id' => 10,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => 'Machine wash cold. Line dry. Iron on reverse.',
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],

            // ── Product 3: Olive Field Jacket ────────────────────────────────
            [
                'product_id' => 3,
                'attribute_id' => 1,
                'option_id' => 3,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 3,
                'attribute_id' => 3,
                'option_id' => null,
                'value_varchar' => '60% Cotton, 40% Polyester Ripstop',
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 3,
                'attribute_id' => 4,
                'option_id' => 16,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 3,
                'attribute_id' => 8,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => 320,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 3,
                'attribute_id' => 9,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => 'An army-inspired field jacket with four flap pockets, a zip-and-snap front and a back hem that is slightly longer for coverage. Water-repellent finish.',
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 3,
                'attribute_id' => 10,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => 'Spot clean or dry clean recommended. Do not tumble dry.',
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],

            // ── Product 4: Midnight Utility Jacket ───────────────────────────
            [
                'product_id' => 4,
                'attribute_id' => 1,
                'option_id' => 1,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 4,
                'attribute_id' => 3,
                'option_id' => null,
                'value_varchar' => '55% Nylon, 45% Cotton Blend',
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 4,
                'attribute_id' => 4,
                'option_id' => 16,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 4,
                'attribute_id' => 8,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => 300,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 4,
                'attribute_id' => 9,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => 'A minimalist utility jacket with a concealed zip front, clean lines and a single interior chest pocket. Pairs easily over any layering piece.',
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 4,
                'attribute_id' => 10,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => 'Machine wash cold. Do not bleach. Tumble dry low.',
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],

            // ── Product 5: Classic White Oxford Shirt ────────────────────────
            [
                'product_id' => 5,
                'attribute_id' => 1,
                'option_id' => 2,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 5,
                'attribute_id' => 3,
                'option_id' => null,
                'value_varchar' => '100% Cotton Oxford Weave',
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 5,
                'attribute_id' => 4,
                'option_id' => 16,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 5,
                'attribute_id' => 5,
                'option_id' => 18,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 5,
                'attribute_id' => 8,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => 130,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 5,
                'attribute_id' => 9,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => 'The white Oxford shirt, perfected. Woven from a medium-weight cotton Oxford cloth, it features a soft button-down collar, a single chest pocket and a curved hem for wearing tucked or untucked.',
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 5,
                'attribute_id' => 10,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => 'Machine wash cold. Tumble dry low. Warm iron.',
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],

            // ── Product 6: Slim Indigo Jeans ─────────────────────────────────
            [
                'product_id' => 6,
                'attribute_id' => 1,
                'option_id' => 7,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 6,
                'attribute_id' => 3,
                'option_id' => null,
                'value_varchar' => '98% Cotton, 2% Elastane Selvedge Denim',
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 6,
                'attribute_id' => 4,
                'option_id' => 15,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 6,
                'attribute_id' => 9,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => 'A slim-fit jean in a mid-indigo wash. Cut from a slightly stretchy selvedge-style denim for all-day comfort. Five-pocket construction, tonal stitching.',
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 6,
                'attribute_id' => 10,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => 'Machine wash cold inside out. Hang dry to preserve colour.',
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],

            // ── Product 7: Tapered Grey Chinos ───────────────────────────────
            [
                'product_id' => 7,
                'attribute_id' => 1,
                'option_id' => 6,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 7,
                'attribute_id' => 3,
                'option_id' => null,
                'value_varchar' => '97% Cotton, 3% Elastane Twill',
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 7,
                'attribute_id' => 4,
                'option_id' => 15,
                'value_varchar' => null,
                'value_text' => null,
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 7,
                'attribute_id' => 9,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => 'A tapered chino in a versatile mid-grey. Flat front, side pockets and a back welt pocket. Sits just below the natural waist with a clean, modern taper.',
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
            [
                'product_id' => 7,
                'attribute_id' => 10,
                'option_id' => null,
                'value_varchar' => null,
                'value_text' => 'Machine wash cold. Tumble dry low. Do not bleach.',
                'value_int' => null,
                'value_decimal' => null,
                'value_datetime' => null,
            ],
        ];

        Capsule::table('eav_product_values')->insert($productValues);
    }
}
