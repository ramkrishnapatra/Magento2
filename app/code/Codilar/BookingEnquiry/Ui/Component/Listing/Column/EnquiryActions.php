<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class EnquiryActions extends Column
{
    private const HANDLE_URL_PATH = 'codilar_bookingenquiry/enquiry/handle';
    private const DELETE_URL_PATH = 'codilar_bookingenquiry/enquiry/delete';

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $entityId = $item['entity_id'];

            if (strtolower((string)($item['status'] ?? '')) !== 'handled') {
                $item[$this->getData('name')]['handle'] = [
                    'href' => $this->urlBuilder->getUrl(self::HANDLE_URL_PATH, ['entity_id' => $entityId]),
                    'label' => __('Handle'),
                    'confirm' => [
                        'title' => __('Handle Enquiry'),
                        'message' => __('Are you sure you want to mark enquiry ID %1 as handled?', $entityId)
                    ]
                ];
            }

            $item[$this->getData('name')]['delete'] = [
                'href' => $this->urlBuilder->getUrl(self::DELETE_URL_PATH, ['entity_id' => $entityId]),
                'label' => __('Delete'),
                'confirm' => [
                    'title' => __('Delete Enquiry'),
                    'message' => __('Are you sure you want to delete enquiry ID %1?', $entityId)
                ]
            ];
        }

        return $dataSource;
    }
}
