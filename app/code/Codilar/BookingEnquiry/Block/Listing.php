<?php
declare(strict_types=1);

namespace Codilar\BookingEnquiry\Block;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;

class Listing extends Template
{
    /**
     * @var Curl
     */
    private Curl $curl;

    /**
     * @var Json
     */
    private Json $json;

    /**
     * @param Template\Context $context
     * @param Curl $curl
     * @param Json $json
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Curl $curl,
        Json $json,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->curl = $curl;
        $this->json = $json;
    }

    /**
     * Fetch queue by calling REST API from Block
     *
     * @return array
     */
    public function getEnquiries(): array
    {
        $apiUrl = $this->getBaseUrl() . 'rest/V1/booking-enquiry/queue';

        try {
            $this->curl->addHeader('Content-Type', 'application/json');
            $this->curl->get($apiUrl);
            $response = $this->curl->getBody();

            return $this->json->unserialize($response) ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }
}
