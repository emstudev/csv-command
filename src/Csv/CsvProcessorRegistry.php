<?php
namespace App\Csv;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class CsvProcessorRegistry
{
    private iterable $processors;
    private LoggerInterface $logger;

    public function __construct(#[TaggedIterator('app.csv_processor')] iterable $processors, LoggerInterface $logger)
    {
        $this->processors = $processors;
        $this->logger = $logger;
    }

    public function getProcessor(string $supplierName): ?ProcessorInterface
    {
        $supplierName = strtolower($supplierName);
        foreach ($this->processors as $processor) {
            if ($processor->supports($supplierName)) {
                return $processor;
            }
        }

        $this->logger->warning(sprintf('Processor for "%s" not found', $supplierName));

        return null;
    }
}
