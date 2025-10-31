<?php
namespace App\Csv\Processor;

use App\Csv\ProcessorInterface;
use App\Entity\StockItem;
use App\Service\Processor\ParsePriceService;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.csv_processor')]
class TrahProcessor implements ProcessorInterface
{
    const string SUPPLIER_NAME = 'trah';
    const string PRODUCER_TO_SKIP = 'narzedzia warsztat';
    const array COLUMNS = [
        'external_id',
        'quantity',
        'price',
        'mpn',
        'ean',
        'producer_name',
    ];
    const string QUANTITY_TO_SWAP = '>10';
    const string FIXED_QUANTITY = '11';

    public function __construct(
        private ParsePriceService $parsePriceService,
    ) {}

    public function supports(string $supplier): bool
    {
        return $supplier === self::SUPPLIER_NAME;
    }
    public function processRow(array $row): ?StockItem
    {
        if (count($row) != count(self::COLUMNS)) {
            throw new InvalidArgumentException('Invalid row format: expected exactly 6 columns.');
        }

        $row = array_map('strtolower', $row);
        $row = array_combine(self::COLUMNS, array_slice($row, 0, 6));

        if (empty($row['mpn']) && empty($row['ean'])) {
            return null;
        }

        if ($row['producer_name'] === self::PRODUCER_TO_SKIP) {
            return null;
        }

        if (($row['quantity'] === self::QUANTITY_TO_SWAP)) {
            $row['quantity'] = self::FIXED_QUANTITY;
        }

        $item = new StockItem();
        $item->setExternalId(str_replace(' ', '', $row['external_id']) ?? null);
        $item->setQuantity((int)($row['quantity'] ?? 0));
        $item->setPrice($this->parsePriceService->parsePrice($row['price'] ?? '0'));
        $item->setMpn($row['mpn'] ?? null);
        $item->setEan(!empty($row['ean']) ? trim(strtolower($row['ean'])) : null);
        $item->setProducerName($row['producer_name'] ?? null);

        return $item;
    }
}
