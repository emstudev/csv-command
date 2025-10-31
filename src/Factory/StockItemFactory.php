<?php

namespace App\Factory;

use App\Entity\StockItem;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

final class StockItemFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'ean' => self::faker()->ean13(),
            'mpn' => self::faker()->bothify('MPN-###'),
            'producerName' => self::faker()->company(),
            'externalId' => self::faker()->uuid(),
            'price' => self::faker()->randomFloat(2, 10, 1000),
            'quantity' => self::faker()->numberBetween(1, 50),
        ];
    }

    protected static function getClass(): string
    {
        return StockItem::class;
    }
}
