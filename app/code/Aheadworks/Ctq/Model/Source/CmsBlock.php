<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\Source;

use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CmsBlock
 * @package Aheadworks\Ctq\Model\Source
 */
class CmsBlock implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $blockCollectionFactory;

    /**
     * @var array
     */
    private $options;

    /**
     * @param CollectionFactory $blockCollectionFactory
     */
    public function __construct(CollectionFactory $blockCollectionFactory)
    {
        $this->blockCollectionFactory = $blockCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $options = $this->blockCollectionFactory->create()->toOptionArray();
            array_unshift(
                $options,
                [
                    'value' => '',
                    'label' => __('Please select a static block')
                ]
            );

            $this->options = $options;
        }

        return $this->options;
    }
}
