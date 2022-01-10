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
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Controller\Adminhtml\File;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\ProductAttachments\Model\File;
use Mageplaza\ProductAttachments\Model\FileFactory;
use RuntimeException;

/**
 * Class InlineEdit
 * @package Mageplaza\ProductAttachments\Controller\Adminhtml\File
 */
class InlineEdit extends Action
{
    /**
     * JSON Factory
     *
     * @var JsonFactory
     */
    public $jsonFactory;

    /**
     * File Factory
     *
     * @var FileFactory
     */
    public $fileFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        FileFactory $fileFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->fileFactory = $fileFactory;

        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $fileItems = $this->getRequest()->getParam('items', []);
        if (!(!empty($fileItems) && $this->getRequest()->getParam('isAjax'))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        $key = array_keys($fileItems);
        $fileId = !empty($key) ? (int)$key[0] : '';
        /** @var File $file */
        $file = $this->fileFactory->create()->load($fileId);
        try {
            $fileData = $fileItems[$fileId];
            $file->addData($fileData)->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithFileId($file, $e->getMessage());
            $error = true;
        } catch (RuntimeException $e) {
            $messages[] = $this->getErrorWithFileId($file, $e->getMessage());
            $error = true;
        } catch (Exception $e) {
            $messages[] = $this->getErrorWithFileId(
                $file,
                __('Something went wrong while saving the File.')
            );
            $error = true;
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add File id to error message
     *
     * @param File $file
     * @param string $errorText
     *
     * @return string
     */
    public function getErrorWithFileId(File $file, $errorText)
    {
        return '[File ID: ' . $file->getId() . '] ' . $errorText;
    }
}
