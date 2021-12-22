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
namespace Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\Adapter;

use Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\AdapterInterface;
use Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\Composer\PackageInfo;
use Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\File\Info as FileInfo;
use Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\Exception\CouldNotDetectGeoDataException;
use GeoIp2\Database\Reader;
use MaxMind\Db\Reader\InvalidDatabaseException;

/**
 * Class GeoIp2
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\Adapter
 */
class GeoIp2 implements AdapterInterface
{
    /**
     * Library composer package name
     */
    const COMPOSER_PACKAGE_NAME = 'geoip2/geoip2';

    const DATABASE_FILE_NAME = 'GeoLite2-Country.mmdb';

    /**
     * @var PackageInfo
     */
    private $packageInfo;

    /**
     * @var FileInfo
     */
    private $fileInfo;

    /**
     * @param PackageInfo $packageInfo
     * @param FileInfo $fileInfo
     */
    public function __construct(
        PackageInfo $packageInfo,
        FileInfo $fileInfo
    ) {
        $this->packageInfo = $packageInfo;
        $this->fileInfo = $fileInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryCode($ip)
    {
        try {
            $reader = new Reader($this->fileInfo->getAbsolutePath(self::DATABASE_FILE_NAME));
            $countryModel = $reader->country($ip);
            return $countryModel->country->isoCode;
        } catch (InvalidDatabaseException $exception) {
            throw new CouldNotDetectGeoDataException(__('Unexpected data is found in database.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return $this->packageInfo->isInstalled(self::COMPOSER_PACKAGE_NAME)
            && $this->fileInfo->isExist(self::DATABASE_FILE_NAME);
    }
}
