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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\File;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;

/**
 * Class Info
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\File
 */
class Info
{
    const BASE_PATH = 'aw_osc/geo_ip';

    /**
     * @var ReadInterface
     */
    private $mediaDirectory;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
    }

    /**
     * Get absolute path to file
     *
     * @param string $path
     * @return string
     */
    public function getAbsolutePath($path = '')
    {
        return $this->mediaDirectory->getAbsolutePath($this->getFilePath($path));
    }

    /**
     * Get file path
     *
     * @param string $path
     * @return string
     */
    private function getFilePath($path)
    {
        return self::BASE_PATH . ($path == '' ? $path : '/' . trim($path, '/'));
    }

    /**
     * Check if file exist
     *
     * @param string $path
     * @return bool
     */
    public function isExist($path)
    {
        return $this->mediaDirectory->isExist($this->getFilePath($path));
    }

    /**
     * Get modification timestamp
     *
     * @param string $path
     * @return int
     * phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
     */
    public function getModificationTimestamp($path)
    {
        return filemtime($this->getAbsolutePath($path));
    }
}
