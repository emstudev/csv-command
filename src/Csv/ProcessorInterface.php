<?php
namespace App\Csv;

use App\Entity\StockItem;

interface ProcessorInterface
{
    public function supports(string $supplier): bool;

    /**
     * Process a CSV row (associative array) and return a StockItem entity (not persisted).
     *
     * @param array $row - associative row: header => value
     * @return StockItem|null - return null to skip row
     */
    public function processRow(array $row): ?StockItem;
}
