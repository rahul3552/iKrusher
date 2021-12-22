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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Controller\Index;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Aheadworks\QuickOrder\Model\Product\Search\ResultProvider;

/**
 * Class Search
 *
 * @package Aheadworks\QuickOrder\Controller\Index
 */
class Search extends Action
{
    /**
     * Search term param
     */
    const SEARCH_TERM_PARAM = 'q';

    /**
     * @var ResultProvider
     */
    private $searchResultProvider;

    /**
     * @param Context $context
     * @param ResultProvider $searchResultProvider
     */
    public function __construct(
        Context $context,
        ResultProvider $searchResultProvider
    ) {
        parent::__construct($context);
        $this->searchResultProvider = $searchResultProvider;
    }

    /**
     * Search products and return results
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $searchTerm = $this->getRequest()->getParam(self::SEARCH_TERM_PARAM);
        $result = $this->searchResultProvider->get($searchTerm);
        return $resultJson->setData($result);
    }
}
