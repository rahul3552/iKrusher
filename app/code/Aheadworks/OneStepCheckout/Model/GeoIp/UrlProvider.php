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
namespace Aheadworks\OneStepCheckout\Model\GeoIp;

use Aheadworks\OneStepCheckout\Model\Config;

/**
 * Class UrlProvider
 * @package Aheadworks\OneStepCheckout\Model\GeoIp
 */
class UrlProvider
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Get download url
     *
     * @return string
     */
    public function getDownloadUrl()
    {
        return 'download.maxmind.com/app/geoip_download?edition_id=GeoLite2-Country&license_key='
            . $this->config->getLicenseKey()
            . '&suffix=tar.gz';
    }
}
