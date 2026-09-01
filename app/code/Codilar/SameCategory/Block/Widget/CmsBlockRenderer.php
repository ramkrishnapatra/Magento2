<?php
declare(strict_types=1);

namespace Codilar\SameCategory\Block\Widget;

use Magento\Cms\Block\Block as CmsBlock;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class CmsBlockRenderer extends Template implements BlockInterface
{
    protected function _toHtml(): string
    {
        $blockId = $this->getData('block_id');
        if (!$blockId) {
            return '';
        }

        $cmsBlock = $this->getLayout()
            ->createBlock(CmsBlock::class)
            ->setBlockId($blockId);

        return $cmsBlock ? $cmsBlock->toHtml() : '';
    }
}
