<?php

namespace App\Csv\Processor;

use App\Csv\ProcessorInterface;
use App\Entity\StockItem;
use App\Service\Processor\ParsePriceService;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.csv_processor')]
class LorotomProcessor implements ProcessorInterface
{
    const string SUPPLIER_NAME = 'lorotom';
    const array COLUMNS = [
        'our_code',
        'producer_code',
        'name',
        'producer',
        'quantity',
        'price',
        'ean'
    ];
    const string QUANTITY_TO_SWAP = '>30';
    const string FIXED_QUANTITY = '31';

    public function __construct(
        private ParsePriceService $parsePriceService,
    ) {}

    public function supports(string $supplier): bool
    {
        return $supplier === self::SUPPLIER_NAME;
    }
    public function processRow(array $row): ?StockItem
    {
        $row = $this->reformatData($row);
        if ($this->isHeader($row[0])) {
            return null;
        }

        if (count($row) != count(self::COLUMNS)) {
            throw new InvalidArgumentException('Invalid row format: expected exactly 7 columns.');
        }

        $row = array_map('strtolower', $row);
        $row = array_combine(self::COLUMNS, array_slice($row, 0, 7));
        if (($row['quantity'] === self::QUANTITY_TO_SWAP)) {
            $row['quantity'] = self::FIXED_QUANTITY;
        }

        $item = new StockItem();
        $item->setExternalId($row['our_code'] ?? null);
        $item->setQuantity((int)($row['quantity'] ?? 0));
        $item->setPrice($this->parsePriceService->parsePrice($row['price'] ?? '0'));
        $item->setMpn($row['producer_code'] ?? null);
        $item->setEan(!empty($row['ean']) ? trim(strtolower($row['ean'])) : null);
        $item->setProducerName($row['producer'] ?? null);

        return $item;
    }

    private function isHeader($row): bool {
        return str_contains($row, 'our_code');
    }

    private function reformatData($row): array
    {
        return explode("\t", $row[0]);
    }
}
