<?php
namespace App\Command;

use App\Csv\CsvProcessorRegistry;
use App\Entity\StockItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-stocks')]
class ImportStocksCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private CsvProcessorRegistry $registry
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Import stocks from CSV file')
            ->addArgument('filepath', InputArgument::REQUIRED, 'Absolute path to CSV file')
            ->addArgument('supplier', InputArgument::REQUIRED, 'Supplier name (used to pick processor)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('filepath');
        $supplier = $input->getArgument('supplier');

        if (!is_readable($file)) {
            $output->writeln('<error>File not found or unreadable: '.$file.'</error>');
            return Command::FAILURE;
        }

        $processor = $this->registry->getProcessor($supplier);
        if (!$processor) {
            $output->writeln('<error>No processor found for supplier '.$supplier.'</error>');
            return Command::FAILURE;
        }

        $handle = fopen($file, 'r');
        if ($handle === false) {
            $output->writeln('<error>Could not open file</error>');
            return Command::FAILURE;
        }

        $rowCount = 0;
        $persisted = 0;

        while (($data = fgetcsv($handle, null, ";")) !== false) {
            $rowCount++;
            $item = $processor->processRow($data);
            if ($item instanceof StockItem) {
                $this->em->persist($item);
                $persisted++;
                if (($persisted % 50) === 0) {
                    $this->em->flush();
                    $this->em->clear();
                }
            }
        }

        fclose($handle);

        $this->em->flush();
        $output->writeln(sprintf('Processed rows: %d, persisted: %d', $rowCount, $persisted));

        return Command::SUCCESS;
    }
}
