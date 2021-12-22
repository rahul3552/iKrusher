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

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\DriverPool;

/**
 * Class Downloader
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\File
 */
class Downloader
{
    /**
     * @var DriverPool
     */
    private $driverPool;

    /**
     * @var Info
     */
    private $fileInfo;

    /**
     * @param DriverPool $driverPool
     * @param Info $fileInfo
     */
    public function __construct(
        DriverPool $driverPool,
        Info $fileInfo
    ) {
        $this->driverPool = $driverPool;
        $this->fileInfo = $fileInfo;
    }

    /**
     * Download file into specific folder
     *
     * @param string $path
     * @param string $pathToSave Relative path
     * @return string
     * @throws FileSystemException
     */
    public function download($path, $pathToSave)
    {
        $httpsDriver = $this->driverPool->getDriver(DriverPool::HTTPS);
        $fileDriver = $this->driverPool->getDriver(DriverPool::FILE);
        if ($httpsDriver->isExists($path)) {
            $absoluteBasePath = $this->fileInfo->getAbsolutePath();
            if (!$fileDriver->isExists($absoluteBasePath)) {
                $fileDriver->createDirectory($absoluteBasePath);
            }
            $httpsDriver->copy($path, $this->fileInfo->getAbsolutePath($pathToSave), $fileDriver);
        }
    }
}
