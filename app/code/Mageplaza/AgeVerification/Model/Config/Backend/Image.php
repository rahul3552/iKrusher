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

namespace Mageplaza\AgeVerification\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\File;

/**
 * Class Image
 * @package Mageplaza\AgeVerification\Model\Config\Backend
 */
class Image extends File
{
    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return string[]
     */
    protected function _getAllowedExtensions()
    {
        return ['svg', 'jpg', 'jpeg', 'gif', 'png'];
    }
}
