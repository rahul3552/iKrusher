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
namespace Aheadworks\OneStepCheckout\Model\Report\Indexer\Action;

use Aheadworks\OneStepCheckout\Model\Report\Indexer\IndexerList;

/**
 * Class Full
 * @package Aheadworks\OneStepCheckout\Model\Report\Indexer\Action
 */
class Full
{
    /**
     * @var IndexerList
     */
    private $indexerList;

    /**
     * @param IndexerList $indexerList
     */
    public function __construct(IndexerList $indexerList)
    {
        $this->indexerList = $indexerList;
    }

    /**
     * Execute Full reindex
     *
     * @return void
     */
    public function execute()
    {
        foreach ($this->indexerList->getIndexers() as $indexer) {
            $indexer->reindexAll();
        }
    }
}
