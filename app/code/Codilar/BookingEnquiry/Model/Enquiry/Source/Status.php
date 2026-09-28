<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Model\Enquiry\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'pending', 'label' => __('Pending')],
            ['value' => 'handled', 'label' => __('Handled')],
        ];
    }
}
