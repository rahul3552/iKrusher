<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductAttachments
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Plugin\Api\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\ProductAttachments\Helper\Data;

/**
 * Class GetAttachment
 * @package Mageplaza\ProductAttachments\Plugin\Api\Model
 */
class GetAttachment
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * GetAttachment constructor.
     *
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param ProductRepositoryInterface $repository
     * @param $result
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function afterGetList(
        ProductRepositoryInterface $repository,
        $result
    ) {
        if (!$this->helperData->isEnabled()) {
            return $result;
        }
        $items = $result->getItems();
        foreach ($items as &$product) {
            $files = $this->getMpFileList($product);

            $product->getExtensionAttributes()->setMpFiles($files);
        }
        $result->setItems($items);

        return $result;
    }

    /**
     * @param ProductRepositoryInterface $repository
     * @param $result
     *
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    public function afterGet(
        ProductRepositoryInterface $repository,
        $result
    ) {
        if (!$this->helperData->isEnabled()) {
            return $result;
        }

        $files = $this->getMpFileList($result);
        $result->getExtensionAttributes()->setMpFiles($files);

        return $result;
    }

    /**
     * @param Product $product
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getMpFileList($product)
    {
        $files = [];

        $fileCollection = $this->helperData->getFileByProduct($product->getId());
        $fileRules = $this->getFileRuleList($product);
        foreach ($fileCollection->getItems() as $file) {
            $files[] = [
                'file_id' => $file->getId(),
                'file_name' => $file->getName(),
                'file_icon' => $file->getFileIconPath()
                    ? $this->helperData->getImageUrl($file->getFileIconPath()) : $this->helperData->getDefaultIconUrl()
            ];
        }
        foreach ($fileRules as $file) {
            $files[] = [
                'file_id' => $file->getId(),
                'file_name' => $file->getName(),
                'file_icon' => $file->getFileIconPath()
                    ? $this->helperData->getImageUrl($file->getFileIconPath()) : $this->helperData->getDefaultIconUrl()
            ];
        }

        return $files;
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public function getFileRuleList($product)
    {
        /** if the product use the current rules */
        $fileInRule = [];

        if ($product !== null) {
            $fileCollection = $this->helperData->getFileByRule();
            foreach ($fileCollection as $file) {
                if ($file->getConditions()->validate($product)) {
                    $fileInRule[] = $file;
                }
            }
        }

        return $fileInRule;
    }
}
