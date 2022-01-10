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

namespace Mageplaza\CustomForm\Plugin\Export;

use Closure;
use Exception;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Model\Export\MetadataProvider as UiMetadataProvider;
use Mageplaza\CustomForm\Helper\Data;

/**
 * Class MetadataProvider
 * @package Mageplaza\CustomForm\Plugin\Export
 */
class MetadataProvider
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * MetadataProvider constructor.
     *
     * @param Data $helperData
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Data $helperData,
        RequestInterface $request,
        StoreManagerInterface $storeManager
    ) {
        $this->helperData   = $helperData;
        $this->request      = $request;
        $this->storeManager = $storeManager;
    }

    /**
     * @param UiMetadataProvider $subject
     * @param Closure $proceed
     * @param DocumentInterface $document
     * @param array $fields
     * @param array $options
     *
     * @return array|mixed
     */
    public function aroundGetRowData(
        UiMetadataProvider $subject,
        Closure $proceed,
        DocumentInterface $document,
        $fields,
        $options
    ) {
        $namespace = $this->request->getParam('namespace');

        if ($this->helperData->isEnabled() && $namespace === 'mageplaza_custom_form_form_listing') {
            $row = [];
            foreach ($fields as $column) {
                $key = $document->getCustomAttribute($column)->getValue();
                if ($column === 'store_ids') {
                    $storeIds = explode(',', $key);
                    if (in_array('0', $storeIds, true)) {
                        $key = __('All Store Views');
                    } else {
                        $storeNames = $this->getStoreNames($storeIds);
                        $key        = implode(',', $storeNames);
                    }
                }
                if ($column === 'ctr') {
                    $key = number_format($key, 2) . '%';
                }
                if (is_array($key)) {
                    $row[] = implode(',', $key);
                } else {
                    $row[] = $key;
                }
            }

            return $row;
        }

        return $proceed($document, $fields, $options);
    }

    /**
     * @param array $storeIds
     *
     * @return array
     */
    public function getStoreNames($storeIds)
    {
        foreach ($storeIds as $key => $storeId) {
            try {
                $storeName = $this->storeManager->getStore($storeId)->getName();
            } catch (Exception $e) {
                $storeName = '';
            }

            $storeIds[$key] = $storeName;
        }

        return $storeIds;
    }
}
