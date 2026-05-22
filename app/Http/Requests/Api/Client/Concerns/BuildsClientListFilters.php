<?php

namespace App\Http\Requests\Api\Client\Concerns;

trait BuildsClientListFilters
{
    protected function mergeClientListFilters(): void
    {
        $parts = [];

        if ($this->filled('search')) {
            $parts[] = 'search|like|'.$this->input('search');
        }

        if ($this->filled('status')) {
            $parts[] = 'status|=|'.$this->input('status');
        }

        if ($parts === []) {
            return;
        }

        $existing = trim((string) $this->input('filters', ''));

        $this->merge([
            'filters' => $existing !== ''
                ? $existing.';'.implode(';', $parts)
                : implode(';', $parts),
        ]);
    }
}
