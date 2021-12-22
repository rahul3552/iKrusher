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
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\GeoIp;

use Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\Composer\PackageInfo;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;

/**
 * Class Install
 *
 * @method string getButtonLabel()
 * @method string getButtonLabelInstalled()
 * @method string getPackageName()
 * @method string getSubmitPath()
 *
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\GeoIp
 */
class Install extends Field
{
    /**
     * @var PackageInfo
     */
    private $packageInfo;

    /**
     * {@inheritdoc}
     */
    protected $_template = 'system/config/geo_ip/install.phtml';

    /**
     * @param Context $context
     * @param PackageInfo $packageInfo
     * @param array $data
     */
    public function __construct(
        Context $context,
        PackageInfo $packageInfo,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->packageInfo = $packageInfo;
    }

    /**
     * Check if disabled
     *
     * @return bool
     */
    public function isDisabled()
    {
        return $this->isInstalled();
    }

    /**
     * Get submit url
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl($this->getSubmitPath());
    }

    /**
     * Check if package installed
     *
     * @return bool
     */
    public function isInstalled()
    {
        return $this->packageInfo->isInstalled($this->getPackageName());
    }

    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(
            [
                'package_name' => $originalData['package_name'],
                'button_label_installed' => $originalData['button_label_installed'],
                'submit_path' => $originalData['submit_path']
            ]
        )->addData(
            [
                'button_label' => $this->isInstalled()
                    ? $originalData['button_label_installed']
                    : $originalData['button_label_not_installed']
            ]
        );
        return $this->_toHtml();
    }
}
