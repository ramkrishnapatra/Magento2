<?php
declare(strict_types=1);

namespace Codilar\StockQty\Model;

use Magento\Customer\Model\AccountManagement as CoreAccountManagement;
use Magento\Customer\Api\Data\CustomerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AccountManagement extends CoreAccountManagement
{
    /**
     * Override authenticate to log manual logins
     *
     * @param string $username
     * @param string $password
     * @return CustomerInterface
     */
    public function authenticate($username, $password)
    {
        $customer = parent::authenticate($username, $password);

        if ($customer && $customer->getId()) {
            $this->logCustomerLoginActivity($customer, 'MANUAL_LOGIN');
        }

        return $customer;
    }

    /**
     * Override createAccount to log auto-login after registration
     *
     * @param CustomerInterface $customer
     * @param string|null $password
     * @param string $redirectUrl
     * @return CustomerInterface
     */
    public function createAccount(CustomerInterface $customer, $password = null, $redirectUrl = '')
    {
        $savedCustomer = parent::createAccount($customer, $password, $redirectUrl);

        if ($savedCustomer && $savedCustomer->getId()) {
            $this->logCustomerLoginActivity($savedCustomer, 'REGISTRATION_AUTO_LOGIN');
        }

        return $savedCustomer;
    }

    /**
     * Write login info into the custom log file (Append Mode)
     *
     * @param CustomerInterface $customer
     * @param string $type
     * @return void
     */
    private function logCustomerLoginActivity(CustomerInterface $customer, string $type): void
    {
        try {
            $customerId    = (int)$customer->getId();
            $customerEmail = (string)$customer->getEmail();
            $loginTime     = (new \DateTime())->format('Y-m-d H:i:s');

            $logEntry = sprintf(
                "[%s] Customer ID: %d | Email: %s | Timestamp: %s",
                $type,
                $customerId,
                $customerEmail,
                $loginTime
            );

            $logFile = BP . '/var/log/customer_login_activity.log';
            $logger  = new Logger('customer_login');
            $logger->pushHandler(new StreamHandler($logFile, Logger::INFO));
            $logger->info($logEntry);

        } catch (\Throwable $e) {
            $logger->info($e->getMessage());
        }
    }
}
