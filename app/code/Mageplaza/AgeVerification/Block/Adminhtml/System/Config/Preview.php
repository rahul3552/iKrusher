<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AgeVerification
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AgeVerification\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Preview
 * @package Mageplaza\AgeVerification\Block\Adminhtml\System\Config
 */
class Preview extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_AgeVerification::preview.phtml';

    /**
     * @param AbstractElement $element
     *
     * @return string
     *
     * @SuppressWarnings(Unused)
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->toHtml();
    }

    /**
     * @param $img
     *
     * @return string
     */
    public function getReviewImgUrl($img)
    {
        return $this->getViewFileUrl('Mageplaza_AgeVerification::media/type/' . $img);
    }
}
