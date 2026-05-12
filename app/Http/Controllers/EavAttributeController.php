<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\EavAttribute;
use App\Models\EavAttributeOption;
use Framework\Request;

class EavAttributeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = EavAttribute::query()->with('options')->orderBy('sort_order')->orderBy('id');
            $attributes = $query->get();

            return $this->success($attributes, 'Attributes retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $id = $request->param('id');
            if (!$id) {
                return $this->error('Attribute ID is required', null, 400);
            }

            $attr = EavAttribute::with('options')->find($id);
            if (!$attr) {
                return $this->error('Attribute not found', null, 404);
            }

            return $this->success($attr, 'Attribute retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->json();
            if (!isset($data['name'], $data['code'])) {
                return $this->error('name and code are required', null, 400);
            }

            $attr = EavAttribute::create([
                'name' => $data['name'],
                'code' => $data['code'],
                'type' => $data['type'] ?? 'varchar',
                'is_required' => $data['is_required'] ?? false,
                'is_filterable' => $data['is_filterable'] ?? false,
                'is_searchable' => $data['is_searchable'] ?? false,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            return $this->success($attr->load('options'), 'Attribute created successfully', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $id = $request->param('id');
            $data = $request->json();
            if (!$id) {
                return $this->error('Attribute ID is required', null, 400);
            }

            $attr = EavAttribute::find($id);
            if (!$attr) {
                return $this->error('Attribute not found', null, 404);
            }

            $attr->update([
                'name' => $data['name'] ?? $attr->name,
                'code' => $data['code'] ?? $attr->code,
                'type' => $data['type'] ?? $attr->type,
                'is_required' => $data['is_required'] ?? $attr->is_required,
                'is_filterable' => $data['is_filterable'] ?? $attr->is_filterable,
                'is_searchable' => $data['is_searchable'] ?? $attr->is_searchable,
                'sort_order' => $data['sort_order'] ?? $attr->sort_order,
            ]);

            return $this->success($attr->load('options'), 'Attribute updated successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $id = $request->param('id');
            if (!$id) {
                return $this->error('Attribute ID is required', null, 400);
            }

            $attr = EavAttribute::find($id);
            if (!$attr) {
                return $this->error('Attribute not found', null, 404);
            }

            $attr->delete();

            return $this->success(null, 'Attribute deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    public function optionsIndex(Request $request)
    {
        try {
            $attributeId = $request->param('attributeId');
            if (!$attributeId) {
                return $this->error('Attribute ID is required', null, 400);
            }

            $attr = EavAttribute::find($attributeId);
            if (!$attr) {
                return $this->error('Attribute not found', null, 404);
            }

            $options = EavAttributeOption::query()
                ->where('attribute_id', $attributeId)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
            ;

            return $this->success($options, 'Options retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    public function optionsStore(Request $request)
    {
        try {
            $attributeId = $request->param('attributeId');
            $data = $request->json();
            if (!$attributeId) {
                return $this->error('Attribute ID is required', null, 400);
            }

            $attr = EavAttribute::find($attributeId);
            if (!$attr) {
                return $this->error('Attribute not found', null, 404);
            }

            if (!isset($data['label'], $data['value'])) {
                return $this->error('label and value are required', null, 400);
            }

            $opt = EavAttributeOption::create([
                'attribute_id' => (int) $attributeId,
                'label' => $data['label'],
                'value' => $data['value'],
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            return $this->success($opt, 'Option created successfully', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    public function optionsUpdate(Request $request)
    {
        try {
            $attributeId = $request->param('attributeId');
            $optionId = $request->param('optionId');
            $data = $request->json();
            if (!$attributeId || !$optionId) {
                return $this->error('Attribute ID and option ID are required', null, 400);
            }

            $opt = EavAttributeOption::query()
                ->where('attribute_id', $attributeId)
                ->where('id', $optionId)
                ->first()
            ;

            if (!$opt) {
                return $this->error('Option not found', null, 404);
            }

            $opt->update([
                'label' => $data['label'] ?? $opt->label,
                'value' => $data['value'] ?? $opt->value,
                'sort_order' => $data['sort_order'] ?? $opt->sort_order,
            ]);

            return $this->success($opt, 'Option updated successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    public function optionsDestroy(Request $request)
    {
        try {
            $attributeId = $request->param('attributeId');
            $optionId = $request->param('optionId');
            if (!$attributeId || !$optionId) {
                return $this->error('Attribute ID and option ID are required', null, 400);
            }

            $opt = EavAttributeOption::query()
                ->where('attribute_id', $attributeId)
                ->where('id', $optionId)
                ->first()
            ;

            if (!$opt) {
                return $this->error('Option not found', null, 404);
            }

            $opt->delete();

            return $this->success(null, 'Option deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }
}
