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
namespace Aheadworks\Ca\Controller\Forbidden;

use Magento\Cms\Helper\Page as PageHelper;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class Index
 *
 * @package Aheadworks\Ca\Controller\Forbidden
 */
class Index extends Action
{
    const FORBIDDEN_PAGE_ID = 'aw-ca-forbidden-page';

    /**
     * @var PageHelper
     */
    private $helper;

    /**
     * @param Context $context
     * @param PageHelper $pageHelper
     */
    public function __construct(
        Context $context,
        PageHelper $pageHelper
    ) {
        parent::__construct($context);
        $this->helper = $pageHelper;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultPage = $this->helper->prepareResultPage($this, self::FORBIDDEN_PAGE_ID);
        if ($resultPage) {
            $resultPage->setStatusHeader(403, '1.1', 'Forbidden');
            return $resultPage;
        }

        throw new NotFoundException(__('Page not found.'));
    }
}
