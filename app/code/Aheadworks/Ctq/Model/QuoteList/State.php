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
namespace Aheadworks\Ctq\Model\QuoteList;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class State
 * @package Aheadworks\Ctq\Model\QuoteList
 */
class State
{
    /**
     * @var Provider
     */
    private $provider;

    /**
     * @var CheckoutSession
     */
    private $session;

    /**
     * @param Provider $provider
     * @param CheckoutSession $session
     */
    public function __construct(
        Provider $provider,
        CheckoutSession $session
    ) {
        $this->provider = $provider;
        $this->session = $session;
    }

    /**
     * Emulate environment with CTQ Quote instance in Checkout Session
     *
     * @param callable $callback
     * @param array $params
     * @return mixed
     * @throws LocalizedException
     */
    public function emulateQuote($callback, $params = [])
    {
        $currentQuote = $this->session->getQuote();
        $replaceQuote = $this->provider->getQuote();
        $this->session->replaceQuote($replaceQuote);
        try {
            $result = call_user_func_array($callback, $params);
        } catch (\Exception $e) {
            $this->session->replaceQuote($currentQuote);
            throw $e;
        }
        $this->session->replaceQuote($currentQuote);

        return $result;
    }
}
