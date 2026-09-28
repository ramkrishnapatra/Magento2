<?php declare(strict_types=1);

namespace Codilar\BookingEnquiry\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class EnquiryActions extends Column
{
    private const EDIT_URL_PATH = 'codilar_bookingenquiry/enquiry/edit';

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
            $item[$this->getData('name')]['edit'] = [
                'href' => $this->urlBuilder->getUrl(
                    self::EDIT_URL_PATH,
                    ['entity_id' => $item['entity_id']]
                ),
                'label' => __('Edit'),
            ];
        }

        return $dataSource;
    }
}
