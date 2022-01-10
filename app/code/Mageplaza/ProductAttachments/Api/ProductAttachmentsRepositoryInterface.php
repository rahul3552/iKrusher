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
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Api;

/**
 * Class ProductAttachmentsRepositoryInterface
 * @package Mageplaza\ProductAttachments\Api
 */
interface ProductAttachmentsRepositoryInterface
{
    /**
     * @param int $fileId
     * @param int $productId
     * @param int $customerId
     *
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function mineDownloadFile($fileId, $productId, $customerId);

    /**
     * @param int $fileId
     * @param int $productId
     *
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function guestDownloadFile($fileId, $productId);
}
