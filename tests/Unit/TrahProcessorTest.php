<?php
namespace App\Tests\Unit;

use App\Csv\Processor\TrahProcessor;
use App\Service\Processor\ParsePriceService;
use PHPUnit\Framework\TestCase;

class TrahProcessorTest extends TestCase
{
    public function testProcessRowCreatesStockItem()
    {
        $parsePriceService = $this->createMock(ParsePriceService::class);
        $parsePriceService->method('parsePrice')->willReturn(12.34);

        $processor = new TrahProcessor($parsePriceService);

        $row = [
            0 => 'abc-123',
            1 => '5',
            2 => '12,34',
            3 => 'MPN-001',
            4 => 'EAN5901234123457',
            5 => 'ACME'
        ];

        $item = $processor->processRow($row);
        $this->assertNotNull($item);
        $this->assertEquals('abc-123', $item->getExternalId());
        $this->assertEquals('mpn-001', $item->getMpn());
        $this->assertEquals('ean5901234123457', $item->getEan());
        $this->assertEquals('acme', $item->getProducerName());
        $this->assertEquals(12.34, $item->getPrice());
        $this->assertEquals(5, $item->getQuantity());
    }

    public function testProcessRowSkipsNarzedziaWarsztat()
    {
        $parsePriceService = $this->createMock(ParsePriceService::class);
        $parsePriceService->method('parsePrice')->willReturn(12.34);

        $processor = new TrahProcessor($parsePriceService);

        $row = [
            0 => 'abc-123',
            1 => '5',
            2 => '12,34',
            3 => 'MPN-001',
            4 => 'EAN5901234123457',
            5 => 'NARZEDZIA WARSZTAT'
        ];

        $item = $processor->processRow($row);
        $this->assertNull($item);
    }

    public function testProcessRowSwapsMoreThan10To11()
    {
        $parsePriceService = $this->createMock(ParsePriceService::class);
        $parsePriceService->method('parsePrice')->willReturn(12.34);

        $processor = new TrahProcessor($parsePriceService);

        $row = [
            0 => 'abc-123',
            1 => '>10',
            2 => '12,34',
            3 => 'MPN-001',
            4 => 'EAN5901234123457',
            5 => 'ACME'
        ];

        $item = $processor->processRow($row);
        $this->assertNotNull($item);
        $this->assertEquals(11, $item->getQuantity());
    }
}

