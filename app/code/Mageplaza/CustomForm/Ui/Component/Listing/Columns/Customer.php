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

namespace Mageplaza\CustomForm\Ui\Component\Listing\Columns;

use Magento\Customer\Helper\View;
use Magento\Customer\Model\Data\CustomerFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Customer
 * @package Mageplaza\CustomForm\Ui\Component\Listing\Columns
 */
class Customer extends Column
{
    /**
     * @var View
     */
    private $customerHelper;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * Customer constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CustomerFactory $customerFactory
     * @param View $customerHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CustomerFactory $customerFactory,
        View $customerHelper,
        array $components = [],
        array $data = []
    ) {
        $this->customerHelper = $customerHelper;
        $this->customerFactory = $customerFactory;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items']) && is_array($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if ($item['customer_id']) {
                    $item[$this->getData('name')] =
                        $this->getCustomerName($item) . ' <' . $item[$this->getData('name')] . '>';
                } else {
                    $item[$this->getData('name')] = __('Guest');
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param $item
     *
     * @return string
     */
    private function getCustomerName($item)
    {
        $item['id'] = $item['customer_id'];
        $customer = $this->customerFactory->create(['data' => $item]);

        return $this->customerHelper->getCustomerName($customer);
    }
}
