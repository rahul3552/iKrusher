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

namespace Mageplaza\ProductAttachments\Block\Adminhtml\File\Edit\Tab;

use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroup;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Mageplaza\ProductAttachments\Model\File as ModelFile;
use Mageplaza\ProductAttachments\Helper\Data as HelperData;
use Mageplaza\ProductAttachments\Helper\File as HelperFile;
use Mageplaza\ProductAttachments\Model\Config\Source\FileAction;
use Mageplaza\ProductAttachments\Model\Config\Source\FileType;
use Mageplaza\ProductAttachments\Model\Config\Source\Groups;
use Mageplaza\ProductAttachments\Model\Config\Source\Icon;
use Mageplaza\ProductAttachments\Model\Config\Source\OrderStatus;
use Mageplaza\ProductAttachments\Model\Config\Source\Status;

/**
 * Class File
 * @package Mageplaza\ProductAttachments\Block\Adminhtml\File\Edit\Tab
 */
class File extends Generic implements TabInterface
{
    /**
     * @var Store
     */
    public $systemStore;

    /**
     * @var HelperData
     */
    public $helperData;

    /**
     * @var Enabledisable
     */
    protected $_enableDisable;

    /**
     * @var Yesno
     */
    protected $_yesNo;

    /**
     * @var Status
     */
    protected $_status;

    /**
     * @var CustomerGroup
     */
    protected $_customerGroup;

    /**
     * @var HelperFile
     */
    protected $_helperFile;

    /**
     * @var Icon
     */
    protected $_iconList;

    /**
     * @var FileAction
     */
    protected $_customerAction;

    /**
     * @var FileType
     */
    protected $_fileType;

    /**
     * @var Groups
     */
    protected $groups;

    /**
     * @var OrderStatus
     */
    protected $orderStatus;

    /**
     * File constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Enabledisable $enableDisable
     * @param Yesno $yesNo
     * @param Store $systemStore
     * @param CustomerGroup $customerGroup
     * @param Status $status
     * @param HelperFile $helperFile
     * @param HelperData $helperData
     * @param Icon $icon
     * @param FileAction $customerAction
     * @param FileType $fileType
     * @param Groups $groups
     * @param OrderStatus $orderStatus
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Enabledisable $enableDisable,
        Yesno $yesNo,
        Store $systemStore,
        CustomerGroup $customerGroup,
        Status $status,
        HelperFile $helperFile,
        HelperData $helperData,
        Icon $icon,
        FileAction $customerAction,
        FileType $fileType,
        Groups $groups,
        OrderStatus $orderStatus,
        array $data = []
    ) {
        $this->_enableDisable  = $enableDisable;
        $this->_yesNo          = $yesNo;
        $this->systemStore     = $systemStore;
        $this->_customerGroup  = $customerGroup;
        $this->_status         = $status;
        $this->_helperFile     = $helperFile;
        $this->helperData      = $helperData;
        $this->_iconList       = $icon;
        $this->_customerAction = $customerAction;
        $this->_fileType       = $fileType;
        $this->groups          = $groups;
        $this->orderStatus     = $orderStatus;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _prepareForm()
    {
        /** @var ModelFile $file */
        $file = $this->_coreRegistry->registry('mageplaza_productattachments_file');

        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('file_');
        $form->setFieldNameSuffix('file');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('General'),
            'class'  => 'fieldset-wide'
        ]);

        $fieldset->addField('label', 'text', [
            'name'     => 'label',
            'label'    => __('File Label'),
            'title'    => __('File Label'),
            'required' => true
        ]);

        $fieldset->addField('name', 'text', [
            'name'     => 'name',
            'label'    => __('File Name'),
            'title'    => __('File Name'),
            'required' => true
        ]);

        $typeSelect = $fieldset->addField('type', 'select', [
            'name'   => 'type',
            'label'  => __('File Type'),
            'title'  => __('File Type'),
            'values' => $this->_fileType->toOptionArray()
        ]);

        $partFile = $fieldset->addField('file_path', Renderer\Image::class, [
            'name'     => 'file_path',
            'label'    => __('File'),
            'title'    => __('File'),
            'path'     => $this->_helperFile->getBaseMediaPath(HelperFile::TEMPLATE_MEDIA_TYPE_FILE),
            'required' => true
        ]);

        $linkFile = $fieldset->addField('file_link', 'text', [
            'name'     => 'file_link',
            'label'    => __('File Link'),
            'title'    => __('File Link'),
            'required' => true,
            'class'    => 'validate-url'
        ]);

        //fix the selected attribute doesn't work due to the name in the database and the name field are different
        $file->setIcon($file->getFileIconPath());

        $fieldset->addField('icon', 'select', [
            'name'   => 'icon',
            'label'  => __('Icon'),
            'title'  => __('Icon'),
            'values' => $this->_iconList->toOptionArray()
        ])->setAfterElementHtml(
            '<img id="mpattachments-icon-sample" width="40px" src="'
            . $this->helperData->getDefaultIconUrl() . '">'
        );

        $fieldset->addField('status', 'select', [
            'name'   => 'status',
            'label'  => __('Status'),
            'title'  => __('Status'),
            'values' => $this->_status->toOptionArray()
        ]);
        if (!$file->hasData('status')) {
            $file->setStatus(1);
        }

        if ($this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField('store_ids', 'hidden', [
                'name'  => 'store_ids',
                'value' => $this->_storeManager->getStore()->getId()
            ]);
        } else {
            /** @var RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()->createBlock(Element::class);
            $fieldset->addField('store_ids', 'multiselect', [
                'name'   => 'store_ids',
                'label'  => __('Store Views'),
                'title'  => __('Store Views'),
                'values' => $this->systemStore->getStoreValuesForForm(false, true)
            ])->setRenderer($rendererBlock);

            if (!$file->hasData('store_ids')) {
                $file->setStoreIds($this->helperData->getDefaultValueConfig('store_view') ?: 0);
            }
        }

        $fieldset->addField('customer_group', 'multiselect', [
            'name'   => 'customer_group',
            'label'  => __('Show files to customer group(s)'),
            'title'  => __('Show files to customer group(s)'),
            'note'   => __('Select customer group(s) to show attachments to.'),
            'values' => $this->_customerGroup->toOptionArray()
        ]);
        if (!$file->hasData('customer_group')) {
            $file->setCustomerGroup($this->helperData->getDefaultValueConfig('customer_group') ?: 0);
        }

        $fieldset->addField('customer_login', 'select', [
            'name'   => 'customer_login',
            'label'  => __('Logged-in customer'),
            'title'  => __('Logged-in customer'),
            'note'   => __('Customer must log in to download/view the file.'),
            'values' => $this->_yesNo->toOptionArray()
        ]);
        if (!$file->hasData('customer_login')) {
            $file->setCustomerLogin($this->helperData->getDefaultValueConfig('is_login') ?: 0);
        }

        $isBuyer = $fieldset->addField('is_buyer', 'select', [
            'name'   => 'is_buyer',
            'label'  => __('Verified buyers'),
            'title'  => __('Verified buyers'),
            'note'   => __('Only available for verified buyers.'),
            'values' => $this->_yesNo->toOptionArray()
        ]);
        if (!$file->hasData('is_buyer')) {
            $file->setIsBuyer($this->helperData->getDefaultValueConfig('is_buyer') ?: 0);
        }

        $orderStatuses = $fieldset->addField('order_statuses', 'multiselect', [
            'name'   => 'order_statuses',
            'label'  => __('Order Status'),
            'title'  => __('Order Status'),
            'note'   => __('When the customer place orders and the order status is satisfied, the attached file can be viewed / downloaded.'),
            'values' => $this->orderStatus->toOptionArray()
        ]);

        if (!$file->hasData('order_statuses')) {
            $file->setOrderStatuses($this->helperData->getDefaultValueConfig('order_status') ?: '');
        }

        $fileAction = $fieldset->addField('file_action', 'select', [
            'name'   => 'file_action',
            'label'  => __('Customer Action'),
            'title'  => __('Customer Action'),
            'values' => $this->_customerAction->toOptionArray()
        ]);
        if (!$file->hasData('file_action')) {
            $file->setFileAction($this->helperData->getDefaultValueConfig('customer_action') ?: 0);
        }

        $fieldset->addField('group', 'select', [
            'name'   => 'group',
            'label'  => __('Select Group'),
            'title'  => __('Select Group'),
            'values' => $this->groups->toOptionArray(),
        ]);

        $fieldset->addField('priority', 'text', [
            'name'  => 'priority',
            'label' => __('Priority'),
            'title' => __('Priority'),
            'class' => 'validate-digits-range',
            'value' => '0',
            'note'  => __('The priority of the file.'),
        ]);

        if ($file->getType() === '1') {
            $file->setFileLink($file->getFilePath());
            $file->setFilePath('');
        }

        $form->addValues($file->getData());
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(Dependence::class)
                ->addFieldMap($typeSelect->getHtmlId(), $typeSelect->getName())
                ->addFieldMap($partFile->getHtmlId(), $partFile->getName())
                ->addFieldMap($linkFile->getHtmlId(), $linkFile->getName())
                ->addFieldMap($fileAction->getHtmlId(), $fileAction->getName())
                ->addFieldMap($isBuyer->getHtmlId(), $isBuyer->getName())
                ->addFieldMap($orderStatuses->getHtmlId(), $orderStatuses->getName())
                ->addFieldDependence($partFile->getName(), $typeSelect->getName(), 0)
                ->addFieldDependence($linkFile->getName(), $typeSelect->getName(), 1)
                ->addFieldDependence($fileAction->getName(), $typeSelect->getName(), 0)
                ->addFieldDependence($orderStatuses->getName(), $isBuyer->getName(), 1)
        );
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
