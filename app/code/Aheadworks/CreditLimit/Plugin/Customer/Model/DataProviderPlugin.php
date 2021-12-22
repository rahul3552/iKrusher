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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Plugin\Customer\Model;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Aheadworks\CreditLimit\Model\Customer\CreditLimit\DataProvider as CreditLimitDataProvider;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class DataProviderPlugin
 *
 * @package Aheadworks\CreditLimit\Plugin\Customer\Model
 */
class DataProviderPlugin
{
    /**
     * @var CreditLimitDataProvider
     */
    private $creditLimitDataProvider;

    /**
     * @param CreditLimitDataProvider $creditLimitDataProvider
     */
    public function __construct(
        CreditLimitDataProvider $creditLimitDataProvider
    ) {
        $this->creditLimitDataProvider = $creditLimitDataProvider;
    }

    /**
     * Provide credit limit data into the Customer Data Provider for use in the Admin form
     *
     * @param AbstractDataProvider $subject
     * @param array $data
     * @return array
     * @throws LocalizedException
     */
    public function afterGetData(AbstractDataProvider $subject, $data)
    {
        foreach ($data as &$fieldData) {
            if (!isset($fieldData['customer']['entity_id'])
                || !isset($fieldData['customer']['website_id'])
            ) {
                continue;
            }

            $creditLimitData = $this->creditLimitDataProvider->getData(
                $fieldData['customer']['entity_id'],
                $fieldData['customer']['website_id']
            );
            $fieldData = array_merge($fieldData, $creditLimitData);
        }

        return $data;
    }
}
