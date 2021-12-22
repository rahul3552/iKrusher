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
namespace Aheadworks\CreditLimit\Ui\Component\Form\Customer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Field;
use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;
use Magento\Framework\Locale\CurrencyInterface;

/**
 * Class CurrencyPriceField
 *
 * @package Aheadworks\CreditLimit\Ui\Component\Form\Customer
 */
class CurrencyPriceField extends Field
{
    /**
     * @var SummaryRepositoryInterface
     */
    private $summaryRepository;

    /**
     * @var CurrencyInterface
     */
    private $currency;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param SummaryRepositoryInterface $summaryRepository
     * @param CurrencyInterface $currency
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SummaryRepositoryInterface $summaryRepository,
        CurrencyInterface $currency,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->summaryRepository = $summaryRepository;
        $this->currency = $currency;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();
        $customerId = $this->context->getRequestParam('id');
        if ($customerId) {
            try {
                $summary = $this->summaryRepository->getByCustomerId($customerId);
                $currency = $this->currency->getCurrency($summary->getCurrency());
                $beforeText = $currency->getSymbol();
            } catch (NoSuchEntityException $noSuchEntityException) {
                $beforeText = '';
            }
            $config = $this->getData('config');
            $config['addbefore'] = $beforeText;
            $this->setData('config', $config);
        }
    }
}
