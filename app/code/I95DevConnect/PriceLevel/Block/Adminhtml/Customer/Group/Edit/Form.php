<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Block\Adminhtml\Customer\Group\Edit;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Customer Group Edit Form
 */
class Form extends \Magento\Customer\Block\Adminhtml\Group\Edit\Form
{

    const GROUP_CODE_MAX_LENGTH = 15;
    const PRICELEVEL_ID = 'pricelevel_id';
    const CUSTOMER_GROUP_CODE = 'customer_group_code';
    const LABEL = 'label';
    const CLASS_STR = 'class';
    const TITLE = 'title';
    const REQUIRED = 'required';
    public $groupName = 'Group Name';

    /**
     *
     * @var \I95DevConnect\MessageQueue\Model\CustomerGroup
     */
    public $customerGroupModel;

    /**
     *
     * @var \I95DevConnect\PriceLevel\Model\PriceLevelData
     */
    public $priceLevelModel;

    /**
     * @var \I95DevConnect\PriceLevel\Helper\Data
     */
    public $helper;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $baseData;

    /**
     * Class constructor to include all the dependencies
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Tax\Model\TaxClass\Source\Customer $taxCustomer
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Customer\Api\Data\GroupInterfaceFactory $groupDataFactory
     * @param \I95DevConnect\MessageQueue\Model\CustomerGroup $customerGroupModel
     * @param \I95DevConnect\PriceLevel\Model\PriceLevelData $priceLevelModel
     * @param \I95DevConnect\PriceLevel\Helper\Data $dataHelper
     * @param \I95DevConnect\MessageQueue\Helper\Data $baseData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Tax\Model\TaxClass\Source\Customer $taxCustomer,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Api\Data\GroupInterfaceFactory $groupDataFactory,
        \I95DevConnect\MessageQueue\Model\CustomerGroup $customerGroupModel,
        \I95DevConnect\PriceLevel\Model\PriceLevelData $priceLevelModel,
        \I95DevConnect\PriceLevel\Helper\Data $dataHelper,
        \I95DevConnect\MessageQueue\Helper\Data $baseData,
        array $data = []
    ) {
        $this->customerGroupModel = $customerGroupModel;
        $this->priceLevelModel = $priceLevelModel;
        $this->helper = $dataHelper;
        $this->baseData = $baseData;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $taxCustomer,
            $taxHelper,
            $groupRepository,
            $groupDataFactory,
            $data
        );
    }

    /**
     * Prepare form to render
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _prepareLayout()
    {
        $component = $this->baseData->getscopeConfig(
            'i95dev_messagequeue/I95DevConnect_settings/component',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
        if (!$this->helper->isEnabled() || $component == 'AX' || $component == 'NAV') {
            parent::_prepareLayout();
            return;
        }
        parent::_prepareLayout();

        $form = $this->_formFactory->create();

        $groupId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_GROUP_ID);
        /**
         * @var \Magento\Customer\Api\Data\GroupInterface $customerGroup
         */
        if ($groupId === null) {
            $customerGroup = $this->groupDataFactory->create();
            $defaultCustomerTaxClass = $this->_taxHelper->getDefaultCustomerTaxClass();
        } else {
            $customerGroup = $this->_groupRepository->getById($groupId);
            $defaultCustomerTaxClass = $customerGroup->getTaxClassId();
        }
        $groupData = $this->customerGroupModel->getCollection()
            ->addFieldToFilter('customer_group_id', $groupId)
            ->getData();
        $priceLevelId = isset($groupData[0][self::PRICELEVEL_ID]) ? $groupData[0][self::PRICELEVEL_ID] : '';
        $priceLevel = $this->priceLevelModel->load($priceLevelId)->getPricelevelCode();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Group Information')]);
        $groupCodeLength = self::GROUP_CODE_MAX_LENGTH;

        $validateClass = sprintf(
            'required-entry validate-length maximum-length-%d',
            $groupCodeLength
        );
        if ($groupId === null) {
            $name = $fieldset->addField(
                self::CUSTOMER_GROUP_CODE,
                'text',
                [
                'name' => 'code',
                self::LABEL => __($groupName),
                self::TITLE => __($groupName),
                'note' => __(
                    'Maximum length must be less then %1 symbols',
                    $groupCodeLength
                ),
                self::CLASS_STR => $validateClass,
                self::REQUIRED => true
                    ]
            );
        } else {
            $name = $fieldset->addField(
                self::CUSTOMER_GROUP_CODE,
                'text',
                [
                'name' => 'code',
                self::LABEL => __($groupName),
                self::TITLE => __($groupName),
                'note' => __(
                    'Maximum length must be less then %1 symbols',
                    $groupCodeLength
                ),
                self::CLASS_STR => $validateClass,
                self::REQUIRED => true,
                'readonly' => true
                    ]
            );
        }

        if (empty($customerGroup->getId()) && $customerGroup->getCode()) {
            $name->setDisabled(true);
        }

        $fieldset->addField(
            'tax_class_id',
            'select',
            [
            'name' => 'tax_class',
            self::LABEL => __('Tax Class'),
            self::TITLE => __('Tax Class'),
            self::CLASS_STR => 'required-entry',
            self::REQUIRED => true,
            'values' => $this->_taxCustomer->toOptionArray(),
                ]
        );

        if ($component != "NAV") {
            $fieldset->addField(
                'price_level',
                'text',
                [
                'name' => self::PRICELEVEL_ID,
                self::LABEL => __('Price Level'),
                self::TITLE => __('Price Level'),
                self::REQUIRED => false,
                'readonly' => true,
                'value' => $priceLevel,
                    ]
            );
        }

        if ($customerGroup->getId() !== null) {
            // If edit add id
            $form->addField('id', 'hidden', ['name' => 'id', 'value' => $customerGroup->getId()]);
        }
        if ($this->_backendSession->getCustomerGroupData()) {
            $form->addValues($this->_backendSession->getCustomerGroupData());
            $this->_backendSession->setCustomerGroupData(null);
        } else {
            $form->addValues(
                [
                        'id' => $customerGroup->getId(),
                        self::CUSTOMER_GROUP_CODE => $customerGroup->getCode(),
                        'tax_class_id' => $defaultCustomerTaxClass,
                    ]
            );
        }

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('customer/*/save'));
        $form->setMethod('post');
        $this->setForm($form);
    }
}
