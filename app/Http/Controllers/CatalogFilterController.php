<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\EavAttribute;
use App\Models\EavAttributeOption;
use Framework\Request;

class CatalogFilterController extends Controller
{
    /**
     * Category names under Men's Clothing (direct children of root) for PLP filters.
     */
    public function categories(Request $request)
    {
        try {
            $items = Category::query()
                ->where('parent_id', 1)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'name'])
            ;

            return $this->success($items, 'Category filters retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * Distinct color labels used by active products (attribute "color").
     */
    public function colors(Request $request)
    {
        try {
            $labels = $this->distinctOptionLabelsForAttribute('color');
            if ([] === $labels) {
                $labels = $this->allOptionLabelsForAttribute('color');
            }

            return $this->success($labels, 'Color filters retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * Distinct apparel size labels (attribute "size") used by active products.
     */
    public function sizes(Request $request)
    {
        try {
            $labels = $this->distinctOptionLabelsForAttribute('size');
            if ([] === $labels) {
                $labels = $this->allOptionLabelsForAttribute('size');
            }

            return $this->success($labels, 'Size filters retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * Filterable select attributes with options for building PLP filters.
     *
     * Optional query: category_id — only include options used by active products in that category.
     */
    public function filterableAttributes(Request $request)
    {
        try {
            $categoryId = $request->input('category_id');
            $categoryId = null !== $categoryId && '' !== $categoryId ? (int) $categoryId : null;

            $attributes = EavAttribute::query()
                ->where('is_filterable', true)
                ->where('type', 'select')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'type', 'sort_order'])
            ;

            $payload = [];
            foreach ($attributes as $attr) {
                if (null !== $categoryId) {
                    $options = $this->optionsForAttributeInCategory((int) $attr->id, $categoryId);
                } else {
                    $options = $this->optionsForAttributeGlobally((int) $attr->id);
                }

                $payload[] = [
                    'id' => (int) $attr->id,
                    'name' => $attr->name,
                    'code' => $attr->code,
                    'type' => $attr->type,
                    'sort_order' => (int) $attr->sort_order,
                    'options' => $options,
                ];
            }

            return $this->success($payload, 'Filterable attributes retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * @return list<array{id: int, label: string, value: string, sort_order: int}>
     */
    private function optionsForAttributeGlobally(int $attributeId): array
    {
        $used = $this->distinctOptionsForAttributeQuery($attributeId, null)->get();
        if ($used->isEmpty()) {
            return $this->allOptionsRowsForAttribute($attributeId);
        }

        return $this->formatOptionRows($used);
    }

    /**
     * @return list<array{id: int, label: string, value: string, sort_order: int}>
     */
    private function optionsForAttributeInCategory(int $attributeId, int $categoryId): array
    {
        $used = $this->distinctOptionsForAttributeQuery($attributeId, $categoryId)->get();
        if ($used->isEmpty()) {
            return [];
        }

        return $this->formatOptionRows($used);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\EavAttributeOption>
     */
    private function distinctOptionsForAttributeQuery(int $attributeId, ?int $categoryId)
    {
        $q = EavAttributeOption::query()
            ->select([
                'eav_attribute_options.id',
                'eav_attribute_options.label',
                'eav_attribute_options.value',
                'eav_attribute_options.sort_order',
            ])
            ->join('eav_product_values as epv', 'eav_attribute_options.id', '=', 'epv.option_id')
            ->join('products as p', 'p.id', '=', 'epv.product_id')
            ->where('eav_attribute_options.attribute_id', $attributeId)
            ->where('p.is_active', true)
            ->when(null !== $categoryId, static function ($q) use ($categoryId): void {
                $q->where('p.category_id', $categoryId);
            })
            ->groupBy(
                'eav_attribute_options.id',
                'eav_attribute_options.label',
                'eav_attribute_options.value',
                'eav_attribute_options.sort_order',
            )
            ->orderBy('eav_attribute_options.sort_order')
            ->orderBy('eav_attribute_options.label')
        ;

        return $q;
    }

    /**
     * @return list<array{id: int, label: string, value: string, sort_order: int}>
     */
    private function allOptionsRowsForAttribute(int $attributeId): array
    {
        $rows = EavAttributeOption::query()
            ->where('attribute_id', $attributeId)
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get(['id', 'label', 'value', 'sort_order'])
        ;

        return $this->formatOptionRows($rows);
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Models\EavAttributeOption>|\Illuminate\Database\Eloquent\Collection<int, \App\Models\EavAttributeOption> $rows
     *
     * @return list<array{id: int, label: string, value: string, sort_order: int}>
     */
    private function formatOptionRows($rows): array
    {
        $out = [];
        foreach ($rows as $row) {
            $out[] = [
                'id' => (int) $row->id,
                'label' => (string) $row->label,
                'value' => (string) $row->value,
                'sort_order' => (int) $row->sort_order,
            ];
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    private function distinctOptionLabelsForAttribute(string $attributeCode): array
    {
        // PostgreSQL: SELECT DISTINCT ... ORDER BY requires every ORDER BY column in the
        // select list. GROUP BY option id achieves unique options with valid ordering.
        return EavAttributeOption::query()
            ->select([
                'eav_attribute_options.id',
                'eav_attribute_options.label',
                'eav_attribute_options.sort_order',
            ])
            ->join('eav_attributes', 'eav_attributes.id', '=', 'eav_attribute_options.attribute_id')
            ->join('eav_product_values as epv', 'eav_attribute_options.id', '=', 'epv.option_id')
            ->join('products as p', 'p.id', '=', 'epv.product_id')
            ->where('eav_attributes.code', $attributeCode)
            ->where('p.is_active', true)
            ->groupBy(
                'eav_attribute_options.id',
                'eav_attribute_options.label',
                'eav_attribute_options.sort_order',
            )
            ->orderBy('eav_attribute_options.sort_order')
            ->orderBy('eav_attribute_options.label')
            ->get()
            ->pluck('label')
            ->values()
            ->all()
        ;
    }

    /**
     * All predefined options for an attribute (when nothing is assigned yet).
     *
     * @return list<string>
     */
    private function allOptionLabelsForAttribute(string $attributeCode): array
    {
        return EavAttributeOption::query()
            ->select([
                'eav_attribute_options.label',
                'eav_attribute_options.sort_order',
            ])
            ->join('eav_attributes', 'eav_attributes.id', '=', 'eav_attribute_options.attribute_id')
            ->where('eav_attributes.code', $attributeCode)
            ->orderBy('eav_attribute_options.sort_order')
            ->orderBy('eav_attribute_options.label')
            ->get()
            ->pluck('label')
            ->values()
            ->all()
        ;
    }
}
