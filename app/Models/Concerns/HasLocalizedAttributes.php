<?php

namespace App\Models\Concerns;

/**
 * Resolve a model attribute to its locale-aware value.
 *
 * Convention: every English field has a sibling `<field>_ar`. When the
 * current locale is `ar` and the Arabic value is non-empty, return it;
 * otherwise fall back to the English value. This keeps every existing
 * EN-only record rendering unchanged.
 */
trait HasLocalizedAttributes
{
    public function localized(string $field): ?string
    {
        if (app()->getLocale() === 'ar') {
            $arField = $field . '_ar';
            $arValue = $this->getAttribute($arField);
            if (!empty($arValue)) {
                return $arValue;
            }
        }

        return $this->getAttribute($field);
    }
}
