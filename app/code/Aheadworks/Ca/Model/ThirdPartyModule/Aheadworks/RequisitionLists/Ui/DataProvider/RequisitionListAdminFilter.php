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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Ui\DataProvider;

use Magento\Framework\Data\Collection;
use Magento\Framework\Api\Filter;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterApplierInterface;

/**
 * Class RequisitionListAdminFilter
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Ui\DataProvider\RequisitionList
 */
class RequisitionListAdminFilter implements FilterApplierInterface
{
    /**
     * {@inheritDoc}
     */
    public function apply(Collection $collection, Filter $filter)
    {
        $whereCondition = sprintf('%s IN (%s)', $filter->getField(), $filter->getValue());
        $collection->getSelect()->where(new \Zend_Db_Expr($whereCondition));
    }
}
