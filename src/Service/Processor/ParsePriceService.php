<?php

namespace App\Service\Processor;

class ParsePriceService
{
    public function parsePrice(string $raw): float
    {
        $clean = preg_replace('/[^\d\-,\.]/', '', $raw);
        $clean = str_replace(',', '.', $clean);
        return (float)$clean;
    }
}
