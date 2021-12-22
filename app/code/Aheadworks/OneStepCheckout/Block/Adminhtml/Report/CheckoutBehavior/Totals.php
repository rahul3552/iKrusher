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
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\Report\CheckoutBehavior;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Class Totals
 *
 * @method string getTotalsColumns()
 *
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\Report\CheckoutBehavior
 */
class Totals extends Template
{
    /**
     * inheritdoc
     */
    protected $_template = 'Aheadworks_OneStepCheckout::report/checkout_behavior/totals.phtml';

    /**
     * @var Provider
     */
    private $provider;

    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @param Context $context
     * @param Provider $provider
     * @param Formatter $formatter
     * @param array $data
     */
    public function __construct(
        Context $context,
        Provider $provider,
        Formatter $formatter,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->provider = $provider;
        $this->formatter = $formatter;
    }

    /**
     * Get totals
     *
     * @return array
     */
    public function getTotals()
    {
        $data = $this->provider->getData();
        return $this->prepareTotalsData($data['totalsItems']);
    }

    /**
     * Prepare totals data
     *
     * @param array $totalsData
     * @return array
     * phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
     */
    private function prepareTotalsData($totalsData)
    {
        $totalsPrepared = [];
        $columns = $this->getTotalsColumns();
        foreach ($totalsData as $field => $value) {
            if (isset($columns[$field])) {
                if (isset($columns[$field]['format'])) {
                    $function = $columns[$field]['format'];
                    $totalsPrepared[$field] = call_user_func([$this, $function], $value);
                } else {
                    $totalsPrepared[$field] = $value;
                }
            }
        }
        return $totalsPrepared;
    }

    /**
     * Get column label
     *
     * @param string $field
     * @return string
     */
    public function getColumnLabel($field)
    {
        $columns = $this->getTotalsColumns();
        return isset($columns[$field]) ? $columns[$field]['label'] : '';
    }

    /**
     * Format percents
     *
     * @param float $value
     * @return string
     */
    public function formatPercents($value)
    {
        return $this->formatter->formatPercents($value);
    }
}
