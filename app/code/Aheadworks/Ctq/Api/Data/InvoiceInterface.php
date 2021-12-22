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
namespace Aheadworks\Ctq\Api\Data;

use Magento\Sales\Api\Data\InvoiceInterface as SalesInvoiceInterface;

/**
 * Interface InvoiceInterface
 * @api
 */
interface InvoiceInterface extends SalesInvoiceInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const AW_CTQ_AMOUNT = 'aw_ctq_amount';
    const BASE_AW_CTQ_AMOUNT = 'base_aw_ctq_amount';
    /**#@-*/
}
