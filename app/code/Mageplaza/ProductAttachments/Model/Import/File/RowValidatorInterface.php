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
 * @package     Mageplaza_ProductAttachments
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Model\Import\File;

use Magento\Framework\Validator\ValidatorInterface;

/**
 * Interface RowValidatorInterface
 * @package Mageplaza\ProductAttachments\Model\Import\CustomerGroup
 */
interface RowValidatorInterface extends ValidatorInterface
{
    const ERROR_INVALID_TITLE = 'InvalidValueTITLE';
    const ERROR_LABEL_IS_EMPTY = 'EmptyLabel';
    const ERROR_FILE_PATH_IS_EMPTY = 'EmptyFilePath';

    /**
     * Initialize validator
     *
     * @param $context
     *
     * @return mixed
     */
    public function init($context);
}
