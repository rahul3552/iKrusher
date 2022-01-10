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
 * @package     Mageplaza_CustomForm
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomForm\Block\Adminhtml;

use Exception;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Mageplaza\CustomForm\Helper\Data;

/**
 * Class Menu
 * @package Mageplaza\CustomForm\Block\Adminhtml
 */
class Menu extends Template
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Menu constructor.
     *
     * @param Context $context
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;

        parent::__construct($context, $data);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getDateRange()
    {
        $dateRange = $this->helperData->getDateRange();
        if ($startDate = $this->getRequest()->getParam('startDate')) {
            $dateRange[0] = $startDate;
        }
        if ($endDate = $this->getRequest()->getParam('endDate')) {
            $dateRange[1] = $endDate;
        }

        return $dateRange;
    }

    /**
     * @return string
     */
    public function getGridName()
    {
        return 'mageplaza_custom_form_form_listing.mageplaza_custom_form_form_listing_data_source';
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDate()
    {
        return Data::jsonEncode($this->helperData->getDateRange());
    }
}
