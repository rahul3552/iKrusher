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

namespace Mageplaza\ProductAttachments\Block\Adminhtml\Product\Form;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroup;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\System\Store as SystemStore;
use Mageplaza\ProductAttachments\Helper\Data;
use Mageplaza\ProductAttachments\Model\Config\Source\FileAction;
use Mageplaza\ProductAttachments\Model\Config\Source\Groups;
use Mageplaza\ProductAttachments\Model\Config\Source\Icon;
use Mageplaza\ProductAttachments\Model\Config\Source\OrderStatus;
use Mageplaza\ProductAttachments\Model\Config\Source\Status;

/**
 * Class Attachments
 * @package Mageplaza\ProductAttachments\Block\Adminhtml\Product\Form
 */
class Attachments extends Template
{
    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'Mageplaza_ProductAttachments::group/attachments.phtml';

    /**
     * @var SystemStore
     */
    protected $_systemStore;

    /**
     * @var EncoderInterface
     */
    protected $_jsonEncode;

    /**
     * @var array
     */
    protected $_customerGroup;

    /**
     * @var Product
     */
    protected $_product;

    /**
     * @var Status
     */
    protected $_fileStatus;

    /**
     * @var Icon
     */
    protected $_iconList;

    /**
     * @var array
     */
    protected $_customerAction;

    /**
     * @var Data
     */
    public $helperData;

    /**
     * @var Groups
     */
    protected $groups;

    /**
     * @var OrderStatus
     */
    protected $orderStatus;

    /**
     * Attachments constructor.
     *
     * @param Context $context
     * @param SystemStore $systemStore
     * @param CustomerGroup $customerGroup
     * @param Product $product
     * @param EncoderInterface $encoder
     * @param Data $helperData
     * @param Status $fileStatus
     * @param Icon $iconList
     * @param FileAction $fileAction
     * @param Groups $groups
     * @param OrderStatus $orderStatus
     * @param array $data
     */
    public function __construct(
        Context $context,
        SystemStore $systemStore,
        CustomerGroup $customerGroup,
        Product $product,
        EncoderInterface $encoder,
        Data $helperData,
        Status $fileStatus,
        Icon $iconList,
        FileAction $fileAction,
        Groups $groups,
        OrderStatus $orderStatus,
        array $data = []
    ) {
        $this->_systemStore    = $systemStore;
        $this->_customerGroup  = $customerGroup;
        $this->_product        = $product;
        $this->_jsonEncode     = $encoder;
        $this->helperData      = $helperData;
        $this->_fileStatus     = $fileStatus;
        $this->_iconList       = $iconList;
        $this->_customerAction = $fileAction;
        $this->groups          = $groups;
        $this->orderStatus     = $orderStatus;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getStoreStructure()
    {
        return $this->_jsonEncode->encode($this->_systemStore->getStoreValuesForForm(false, true));
    }

    /**
     * @return string
     */
    public function getIconList()
    {
        $iconList = $this->_iconList->toOptionArray();
        array_shift($iconList);
        $iconList[] = [
            'value' => 'mp_attachment_default_icon',
            'label' => __('Default Icon')
        ];

        return $this->_jsonEncode->encode($iconList);
    }

    /**
     * @return string
     */
    public function getCustomerAction()
    {
        return $this->_jsonEncode->encode($this->_customerAction->toOptionArray());
    }

    /**
     * @return string
     */
    public function getCustomerGroup()
    {
        return $this->_jsonEncode->encode($this->_customerGroup->toOptionArray());
    }

    /**
     * @return string
     */
    public function getGroups()
    {
        return $this->_jsonEncode->encode($this->groups->toOptionArray());
    }

    /**
     * @return string
     */
    public function getOrderStatuses()
    {
        return $this->_jsonEncode->encode($this->orderStatus->toOptionArray());
    }

    /**
     * @return string
     */
    public function getFileStatus()
    {
        return $this->_jsonEncode->encode($this->_fileStatus->toOptionArray());
    }

    /**
     * @return AbstractCollection
     */
    public function getFileCollection()
    {
        $currentProductId = (int) $this->getRequest()->getParam('id');

        return $this->helperData->getFilesByProductId($currentProductId);
    }

    /**
     * Get current product attachment location
     *
     * @return int|AttributeInterface|null
     */
    public function getCurrentLocation()
    {
        $currentProductId = (int) $this->getRequest()->getParam('id');
        /** @var Product $currentProduct */
        $currentProduct = $this->_product->load($currentProductId);
        $attachValue    = $currentProduct->getCustomAttribute(Data::ATTACHMENTS_LOCATION_ATTRIBUTE_CODE);

        return $attachValue === null ? null : $attachValue->getValue();
    }

    /**
     * @return Data
     */
    public function getHelper()
    {
        return $this->helperData;
    }

    /**
     * @return array|mixed
     */
    public function getDefaultShowOn()
    {
        return $this->getHelper()->getConfigGeneral('show_on');
    }
}
