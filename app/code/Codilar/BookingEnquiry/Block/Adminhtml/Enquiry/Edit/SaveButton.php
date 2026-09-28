<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Block\Adminhtml\Enquiry\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        return [
            'label' => __('Save Enquiry'),
            'class' => 'save primary',
            'data_attribute' => ['mage-init' => ['button' => ['event' => 'save']]],
            'sort_order' => 90,
        ];
    }
}
