<?php
declare(strict_types=1);

namespace Codilar\ProductEnquiry\Model\Logger;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class EnquiryLogger
{
    private Logger $logger;

    public function __construct(string $name = 'product_enquiry')
    {
        $this->logger = new Logger($name);
        $streamHandler = new StreamHandler(
            BP . '/var/log/productEnquiry.log',
            Logger::DEBUG
        );
        $this->logger->pushHandler($streamHandler);
    }

    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }
}
