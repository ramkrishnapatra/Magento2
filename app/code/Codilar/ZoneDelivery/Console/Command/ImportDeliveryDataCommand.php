<?php
declare(strict_types=1);

namespace Codilar\ZoneDelivery\Console\Command;

use Codilar\ZoneDelivery\Model\Import\DeliveryDataProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportDeliveryDataCommand
 * CLI command to import delivery zones, pincodes, rates, and delivery slots from CSV (BRD-04)
 */
class ImportDeliveryDataCommand extends Command
{
    private const ARGUMENT_FILE_PATH = 'filepath';

    /**
     * @var DeliveryDataProcessor
     */
    private DeliveryDataProcessor $processor;

    /**
     * @param DeliveryDataProcessor $processor
     * @param string|null $name
     */
    public function __construct(
        DeliveryDataProcessor $processor,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->processor = $processor;
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName('magecafe:delivery:import')
            ->setDescription('Import delivery zones, pincodes, rates, and slots from CSV file (BRD-04)')
            ->addArgument(
                self::ARGUMENT_FILE_PATH,
                InputArgument::REQUIRED,
                'Absolute or relative path to the CSV data file'
            );
        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = (string)$input->getArgument(self::ARGUMENT_FILE_PATH);

        $output->writeln('<info>==================================================</info>');
        $output->writeln('<info>  MageCafe Zone Delivery Data Import (BRD-04)     </info>');
        $output->writeln('<info>==================================================</info>');
        $output->writeln(sprintf('<comment>Target File: %s</comment>', $filePath));

        try {
            $result = $this->processor->process($filePath);

            if (!empty($result['errors'])) {
                $output->writeln('<error>Import Aborted: Validation errors detected (BR-07)</error>');
                $output->writeln('<comment>No changes were committed to the database.</comment>');
                foreach ($result['errors'] as $error) {
                    $output->writeln(sprintf('  - <error>%s</error>', $error));
                }
                return Command::FAILURE;
            }

            $output->writeln('<info>Import completed successfully!</info>');
            $output->writeln(sprintf('- Unique Zones Created/Updated: %d', $result['zones']));
            $output->writeln(sprintf('- Pincodes Mapped: %d', $result['pincodes']));
            $output->writeln(sprintf('- Rate Tiers Configured: %d', $result['rates']));
            $output->writeln(sprintf('- Delivery Slots Established: %d', $result['slots']));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>System Failure: %s</error>', $e->getMessage()));
            return Command::FAILURE;
        }
    }
}
