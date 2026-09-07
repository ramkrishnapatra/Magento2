<?php
declare(strict_types=1);

namespace Codilar\ProductEnquiry\Console\Command;

use Codilar\ProductEnquiry\Api\EnquiryRepositoryInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CleanEnquiriesCommand extends Command
{
    private EnquiryRepositoryInterface $enquiryRepository;
    private State $appState;

    public function __construct(
        EnquiryRepositoryInterface $enquiryRepository,
        State $appState,
        string $name = null
    ) {
        $this->enquiryRepository = $enquiryRepository;
        $this->appState = $appState;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('enquiry:create')
            ->setDescription('Interactive CLI prompt to create a new product enquiry.');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->appState->setAreaCode(Area::AREA_GLOBAL);
        } catch (\Exception $e) {
            // Area code already set in CLI context
        }

        // Setup custom Monolog logger pointing to var/log/productEnquiry.log
        $writer = new StreamHandler(BP . '/var/log/productEnquiry.log', Logger::DEBUG);
        $logger = new Logger('product_enquiry_cli');
        $logger->pushHandler($writer);

        $helper = $this->getHelper('question');

        $output->writeln('<info>===========================================</info>');
        $output->writeln('<info>       CREATE PRODUCT ENQUIRY (CLI)       </info>');
        $output->writeln('<info>===========================================</info>');

        // 1. Ask SKU
        $skuQuestion = new Question('<question>Enter Product SKU: </question>');
        $skuQuestion->setValidator(function ($answer) {
            if (empty(trim((string)$answer))) {
                throw new \RuntimeException('Product SKU cannot be empty.');
            }
            return trim((string)$answer);
        });
        $sku = $helper->ask($input, $output, $skuQuestion);

        // 2. Ask Customer Name
        $nameQuestion = new Question('<question>Enter Customer Name: </question>');
        $nameQuestion->setValidator(function ($answer) {
            if (empty(trim((string)$answer))) {
                throw new \RuntimeException('Customer Name cannot be empty.');
            }
            return trim((string)$answer);
        });
        $name = $helper->ask($input, $output, $nameQuestion);

        // 3. Ask Customer Email
        $emailQuestion = new Question('<question>Enter Customer Email: </question>');
        $emailQuestion->setValidator(function ($answer) {
            $value = trim((string)$answer);
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException('Please enter a valid email address.');
            }
            return $value;
        });
        $email = $helper->ask($input, $output, $emailQuestion);

        // 4. Ask Quantity (Default: 1)
        $qtyQuestion = new Question('<question>Enter Quantity [1]: </question>', '1');
        $qtyQuestion->setValidator(function ($answer) {
            $intVal = (int)$answer;
            if ($intVal < 1) {
                throw new \RuntimeException('Quantity must be at least 1.');
            }
            return $intVal;
        });
        $qty = (int)$helper->ask($input, $output, $qtyQuestion);

        // 5. Ask Customer Address
        $addressQuestion = new Question('<question>Enter Customer Address: </question>');
        $addressQuestion->setValidator(function ($answer) {
            if (empty(trim((string)$answer))) {
                throw new \RuntimeException('Address cannot be empty.');
            }
            return trim((string)$answer);
        });
        $address = $helper->ask($input, $output, $addressQuestion);

        $output->writeln('');
        $output->writeln('<comment>Processing and saving enquiry...</comment>');

        // Log CLI input hit
        $logger->info(sprintf(
            '[CLI HIT] - SKU: %s, Name: %s, Email: %s, Qty: %d, Address: %s',
            $sku,
            $name,
            $email,
            $qty,
            $address
        ));

        try {
            $enquiry = $this->enquiryRepository->create();
            $enquiry->setSku($sku)
                ->setName($name)
                ->setEmail($email)
                ->setAddress($address)
                ->setQty($qty);

            $savedEnquiry = $this->enquiryRepository->save($enquiry);
            $entityId = $savedEnquiry->getId();

            // Log Success
            $logger->info('[CLI DB Save SUCCESS] - Entity ID: ' . $entityId);

            $output->writeln(sprintf(
                '<info>Success: Enquiry successfully created with Entity ID: %d!</info>',
                $entityId
            ));

            return Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            // Log Error
            $logger->error('[CLI DB Save FAILED] - ' . $e->getMessage());

            $output->writeln('<error>Error saving enquiry: ' . $e->getMessage() . '</error>');
            return Cli::RETURN_FAILURE;
        }
    }
}
