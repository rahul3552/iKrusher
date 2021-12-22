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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Ui\DataProvider\DefaultFilter\WebsiteId;

use Aheadworks\OneStepCheckout\Model\ResourceModel\Report\FilterableInterface;
use Aheadworks\OneStepCheckout\Ui\DataProvider\DefaultFilter\WebsiteId as Filter;
use Aheadworks\OneStepCheckout\Ui\DataProvider\DefaultFilterApplierInterface;

/**
 * Class Applier
 * @package Aheadworks\OneStepCheckout\Ui\DataProvider\DefaultFilter\WebsiteId
 */
class Applier implements DefaultFilterApplierInterface
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @param Filter $filter
     */
    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(FilterableInterface $collection)
    {
        $filterValue = $this->filter->getValue();
        if ($filterValue != 0) {
            $collection->addWebsiteIdFilter($filterValue);
        }
    }
}
