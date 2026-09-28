<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Block\Adminhtml\Enquiry\Edit;

use Magento\Backend\Block\Widget\Context;

class GenericButton
{
    public function __construct(
        protected readonly Context $context
    ) {
    }

    public function getEnquiryId(): ?int
    {
        $id = $this->context->getRequest()->getParam('entity_id');
        return $id ? (int) $id : null;
    }

    public function getUrl(string $route = '*/*/', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
