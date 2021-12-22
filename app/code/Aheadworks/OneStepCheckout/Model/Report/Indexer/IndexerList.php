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
namespace Aheadworks\OneStepCheckout\Model\Report\Indexer;

use Aheadworks\OneStepCheckout\Model\ResourceModel\Report\Indexer\IndexerInterface;
use Aheadworks\OneStepCheckout\Model\ResourceModel\Report\Indexer\AbandonedCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class IndexerList
 * @package Aheadworks\OneStepCheckout\Model\Report\Indexer
 */
class IndexerList
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $indexers = [
        'abandoned_checkout' => AbandonedCheckout::class
    ];

    /**
     * @var array
     */
    private $indexerInstances = [];

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Retrieve indexer instance
     *
     * @param string $code
     * @return IndexerInterface
     * @throws \Exception
     */
    public function getIndexer($code)
    {
        if (!isset($this->indexerInstances[$code])) {
            $indexerInstance = $this->objectManager->create($this->indexers[$code]);
            if (!$indexerInstance instanceof IndexerInterface) {
                throw new LocalizedException(
                    sprintf('Indexer %s does not implement required interface.', $code)
                );
            }
            $this->indexerInstances[$code] = $indexerInstance;
        }
        return $this->indexerInstances[$code];
    }

    /**
     * Retrieve indexers instances
     *
     * @return IndexerInterface[]
     */
    public function getIndexers()
    {
        $indexers = [];
        foreach (array_keys($this->indexers) as $code) {
            $indexers[$code] = $this->getIndexer($code);
        }
        return $indexers;
    }
}
