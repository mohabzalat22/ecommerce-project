<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\TaxSetting;
use Framework\Request;

class TaxSettingsController extends Controller
{
    public function show()
    {
        try {
            return $this->success($this->settingsPayload(), 'Tax settings retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $data = $request->json();
            if (!array_key_exists('tax_enabled', $data)) {
                return $this->error('tax_enabled is required', null, 400);
            }

            $taxEnabled = filter_var($data['tax_enabled'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if (null === $taxEnabled) {
                return $this->error('tax_enabled must be a boolean value', null, 422);
            }

            TaxSetting::query()->updateOrCreate(
                ['key' => TaxSetting::TAX_ENABLED_KEY],
                ['value' => $taxEnabled ? '1' : '0'],
            );

            return $this->success($this->settingsPayload(), 'Tax settings updated successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    private function settingsPayload(): array
    {
        $rawValue = TaxSetting::query()->where('key', TaxSetting::TAX_ENABLED_KEY)->value('value');

        return [
            'tax_enabled' => filter_var($rawValue, FILTER_VALIDATE_BOOLEAN),
            'tax_rate_percent' => 14,
            'tax_enabled_key' => TaxSetting::TAX_ENABLED_KEY,
        ];
    }
}