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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Block\QuoteList\Item\Renderer\Actions;

use Aheadworks\Ctq\Model\Request\Checker;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic;
use Magento\Framework\Url\Helper\Data as UrlData;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Remove
 * @package Aheadworks\Ctq\Block\Cart\Item\Renderer\Actions
 */
class Remove extends Generic
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var UrlData
     */
    private $urlData;

    /**
     * @var Json
     */
    private $jsonEncoder;

    /**
     * @param Template\Context $context
     * @param UrlInterface $urlBuilder
     * @param UrlData $urlData
     * @param Json $jsonEncoder
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        UrlInterface $urlBuilder,
        UrlData $urlData,
        Json $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlBuilder = $urlBuilder;
        $this->urlData = $urlData;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * Get json data for initialize delete button
     *
     * @return string
     */
    public function getDeletePostJson()
    {
        $url = $this->urlBuilder->getUrl(
            'checkout/cart/delete/',
            [
                Checker::AW_CTQ_QUOTE_LIST_FLAG => 1
            ]
        );
        $data = [
            'id' => $this->getItem()->getId()
        ];

        if (!$this->_request->isAjax()) {
            $data[ActionInterface::PARAM_NAME_URL_ENCODED] = $this->urlData->getCurrentBase64Url();
        }
        return $this->jsonEncoder->serialize(['action' => $url, 'data' => $data]);
    }
}
