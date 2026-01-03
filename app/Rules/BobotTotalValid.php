<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BobotTotalValid implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // This rule should be used with custom validator in controller
        // Example: $validator->after(function ($validator) { ... })
    }

    /**
     * Check if all kriteria bobot sum to 1.0
     * 
     * Usage in controller:
     * $validator->after(function ($validator) {
     *     $totalBobot = Kriteria::where('aktif', true)->sum('bobot');
     *     if (round($totalBobot, 2) != 1.0) {
     *         $validator->errors()->add('bobot', 
     *             'Total bobot semua kriteria harus = 1.0 (saat ini: ' . $totalBobot . ')');
     *     }
     * });
     */
}
