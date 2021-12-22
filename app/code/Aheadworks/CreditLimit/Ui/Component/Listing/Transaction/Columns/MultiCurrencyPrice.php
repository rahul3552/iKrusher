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
namespace Aheadworks\CreditLimit\Ui\Component\Listing\Transaction\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Aheadworks\CreditLimit\Model\Source\Transaction\Action as TransactionActionSource;
use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Model\Transaction\Balance\Formatter;

/**
 * Class MultiCurrencyPrice
 *
 * @package Aheadworks\CreditLimit\Ui\Component\Listing\Transaction\Columns
 */
class MultiCurrencyPrice extends Column
{
    /**
     * @var TransactionActionSource
     */
    private $actionSource;

    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param TransactionActionSource $actionSource
     * @param Formatter $formatter
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        TransactionActionSource $actionSource,
        Formatter $formatter,
        array $components = [],
        array $data = []
    ) {
        $this->actionSource = $actionSource;
        $this->formatter = $formatter;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $price = $item[$this->getData('name')];
                $showPlus = $this->getData('config/showPlus') && $price >= 0;
                $item['row_сlass_' . $this->getData('name')] = $price >= 0
                    ? 'aw_cl__price-green'
                    : 'aw_cl__price-red';

                if (in_array(
                    $item[TransactionInterface::ACTION],
                    $this->actionSource->getActionsToUpdateCreditBalance()
                )) {
                    $formattedAmount = $this->formatter->formatTransactionAmount($item);
                    $item[$this->getData('name')] = ($showPlus ? '+' : '') . $formattedAmount;
                } else {
                    $item[$this->getData('name')] = '';
                }
            }
        }

        return $dataSource;
    }
}
