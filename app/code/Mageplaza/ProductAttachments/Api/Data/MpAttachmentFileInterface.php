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

namespace Mageplaza\ProductAttachments\Api\Data;

/**
 * Interface MpAttachmentFileInterface
 * @package Mageplaza\ProductAttachments\Api\Data
 */
interface MpAttachmentFileInterface
{
    const FILE_ID   = 'file_id';
    const FILE_NAME = 'file_name';
    const ICON_PATH = 'icon_path';

    /**
     * @return int
     */
    public function getFileId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setFileId($value);

    /**
     * @return string
     */
    public function getFileName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setFileName($value);

    /**
     * @return string
     */
    public function getIconPath();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setIconPath($value);
}
