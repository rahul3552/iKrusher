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

namespace Mageplaza\ProductAttachments\Ui\Component\Listing\Columns;

use Magento\Backend\Block\Template;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mageplaza\ProductAttachments\Helper\Data;
use Mageplaza\ProductAttachments\Model\Config\Source\FileAction;

/**
 * Class Attachments
 * @package Mageplaza\ProductAttachments\Ui\Component\Listing\Columns
 */
class Attachments extends Column
{
    /**
     * @var EncoderInterface
     */
    protected $_jsonEncode;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var ProductFactory
     */
    protected $_product;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Attachments constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param EncoderInterface $encoder
     * @param ObjectManagerInterface $objectManager
     * @param UrlInterface $urlBuilder
     * @param ProductFactory $product
     * @param Data $helperData
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        EncoderInterface $encoder,
        ObjectManagerInterface $objectManager,
        UrlInterface $urlBuilder,
        ProductFactory $product,
        Data $helperData,
        array $components = [],
        array $data = []
    ) {
        $this->_jsonEncode = $encoder;
        $this->_objectManager = $objectManager;
        $this->_urlBuilder = $urlBuilder;
        $this->_product = $product;
        $this->_helperData = $helperData;

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
        $blockTemplate = $this->_objectManager->create(Template::class);
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $fileList = $this->_helperData->getFilesByProductId($item['entity_id']);
                $item[$this->getData('name')] = '<a id="mp-attachments-'
                    . $item['entity_id'] . '" style="text-align: center;" href="#">' . __('Add File') . '</a>';
                $item['mp_attachment_file_data'] = $this->prepareFileData($fileList->getData());
                $item['mp_attachment_file_system'] =
                    $this->_jsonEncode->encode($this->_helperData->getShowOnLocation());
                $item['mp_attachment_loading_url'] = $blockTemplate->getViewFileUrl('images/loader-1.gif');
                $item['mp_attachment_ajax_url'] = $this->_urlBuilder->getUrl(
                    'mpproductattachments/file_attachment/upload',
                    ['form_key' => $blockTemplate->getFormKey()]
                );
                $item['mp_attachment_ajax_save_url'] = $this->_urlBuilder->getUrl(
                    'mpproductattachments/product_grid/save',
                    ['form_key' => $blockTemplate->getFormKey()]
                );
                $item['mp_attachment_ajax_get_config'] = $this->_urlBuilder->getUrl(
                    'mpproductattachments/product_grid/config',
                    ['form_key' => $blockTemplate->getFormKey()]
                );
                $item['mp_attachment_location'] = $this->getAttachmentLocation($item['entity_id']);
            }
        }

        return $dataSource;
    }

    /**
     * @param $collection
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function prepareFileData($collection)
    {
        $data = [];
        if (isset($collection) && count($collection) > 0) {
            foreach ($collection as $item) {
                $data[] = [
                    'file_id' => $item['file_id'],
                    'file_name' => $item['name'],
                    'file_label' => $item['label'],
                    'file_size' => $this->_helperData->fileSizeFormat($item['size']),
                    'file_path' => $item['file_path'],
                    'file_icon_path' => $item['file_icon_path'],
                    'file_customer_login' => $item['customer_login'],
                    'file_customer_group' => $item['customer_group'],
                    'file_priority' => $item['priority'],
                    'file_is_buyer' => $item['is_buyer'],
                    'file_status' => $item['status'],
                    'file_stores' => $item['store_ids'],
                    'file_position' => $item['position'],
                    'file_action' => ($item['file_action'] === FileAction::VIEWONLINE) ? __('View Online') : __('Download'),
                    'file_url' => !empty($item['file_icon_path'])
                        ? $this->_helperData->getImageUrl($item['file_icon_path'])
                        : $this->_helperData->getDefaultIconUrl()
                ];
            }
        }

        return $this->_jsonEncode->encode($data);
    }

    /**
     * Get current product attachment location
     *
     * @param $productId
     *
     * @return mixed|null
     */
    public function getAttachmentLocation($productId)
    {
        $product = $this->_product->create()->load($productId);
        $customAttr = $product->getCustomAttribute(Data::ATTACHMENTS_LOCATION_ATTRIBUTE_CODE);

        return ($customAttr === null) ? null : $customAttr->getValue();
    }
}
