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
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Ui;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class CreditLimitFormModifier
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Ui
 */
class CreditLimitFormModifier implements ModifierInterface
{
    /**
     * @var Manager
     */
    private $thirdPartyModuleManager;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param Manager $thirdPartyModuleManager
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Manager $thirdPartyModuleManager,
        ObjectManagerInterface $objectManager
    ) {
        $this->thirdPartyModuleManager = $thirdPartyModuleManager;
        $this->objectManager = $objectManager;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        if ($this->thirdPartyModuleManager->isAwCreditLimitModuleEnabled()) {
            if (isset($data['id']) && isset($data['website_id'])) {
                $dataProvider = $this->objectManager->get(
                    \Aheadworks\CreditLimit\Model\Customer\CreditLimit\DataProvider::class
                );
                $creditLimitData = $dataProvider->getData(
                    $data['id'],
                    $data['website_id']
                );
                $creditLimitData['is_data_new'] = false;
            } else {
                $creditLimitData['aw_credit_limit'] = [
                    'is_data_new' => true,
                    'company_id' => 'not_set'
                ];
            }
            $data = array_merge($data, $creditLimitData);
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
