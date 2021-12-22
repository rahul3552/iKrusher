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
namespace Aheadworks\Ctq\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;

/**
 * Class AdminUserSelector
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\System\Config\Form\Field
 */
class AdminUserSelector extends Field
{
    /**
     * @inheritdoc
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->prepareElement($element);
        return $element->getElementHtml();
    }

    /**
     * Extension point for plugins to prepare element
     *
     * @param AbstractElement $element
     * @return AbstractElement
     */
    public function prepareElement(AbstractElement $element)
    {
        return $element;
    }
}
