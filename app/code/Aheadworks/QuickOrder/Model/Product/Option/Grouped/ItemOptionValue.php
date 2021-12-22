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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Model\Product\Option\Grouped;

use Aheadworks\QuickOrder\Api\Data\GroupedItemOptionValueInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class ItemOptionValue
 *
 * @package Aheadworks\QuickOrder\Model\Product\Option\Grouped
 */
class ItemOptionValue extends AbstractExtensibleModel implements GroupedItemOptionValueInterface
{
    /**
     * @inheritdoc
     */
    public function getOptionId()
    {
        return $this->getData(self::OPTION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOptionId($value)
    {
        return $this->setData(self::OPTION_ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function getOptionValue()
    {
        return $this->getData(self::OPTION_VALUE);
    }

    /**
     * @inheritdoc
     */
    public function setOptionValue($value)
    {
        return $this->setData(self::OPTION_VALUE, $value);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\QuickOrder\Api\Data\GroupedItemOptionValueExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
