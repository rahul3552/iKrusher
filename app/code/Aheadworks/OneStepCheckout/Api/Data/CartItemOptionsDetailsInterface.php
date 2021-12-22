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
namespace Aheadworks\OneStepCheckout\Api\Data;

/**
 * Interface CartItemOptionsDetailsInterface
 * @package Aheadworks\OneStepCheckout\Api\Data
 */
interface CartItemOptionsDetailsInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const OPTIONS_DETAILS = 'options_details';
    const IMAGE_DETAILS = 'image_details';
    const PAYMENT_DETAILS = 'payment_details';
    /**#@-*/

    /**
     * Get option details
     *
     * @return string
     */
    public function getOptionsDetails();

    /**
     * Set option details
     *
     * @param string $optionDetails
     * @return $this
     */
    public function setOptionsDetails($optionDetails);

    /**
     * Get image details
     *
     * @return string
     */
    public function getImageDetails();

    /**
     * Set image details
     *
     * @param string $imageDetails
     * @return $this
     */
    public function setImageDetails($imageDetails);

    /**
     * Get payment details
     *
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function getPaymentDetails();

    /**
     * Set payment details
     *
     * @param \Magento\Checkout\Api\Data\PaymentDetailsInterface $paymentDetails
     * @return $this
     */
    public function setPaymentDetails($paymentDetails);
}
