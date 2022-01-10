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
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\ProductAttachments\Controller\Adminhtml\File;
use Mageplaza\ProductAttachments\Helper\File as HelperFile;
use Mageplaza\ProductAttachments\Model\File as FileModel;
use Mageplaza\ProductAttachments\Model\FileFactory;
use RuntimeException;

/**
 * Class Save
 * @package Mageplaza\ProductAttachments\Controller\Adminhtml\File
 */
class Save extends File
{
    /**
     * JS helper
     *
     * @var Js
     */
    public $jsHelper;

    /**
     * @var DateTime
     */
    public $date;

    /**
     * @var HelperFile
     */
    protected $_helperFile;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FileFactory $fileFactory
     * @param Js $jsHelper
     * @param DateTime $date
     * @param HelperFile $helperFile
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FileFactory $fileFactory,
        Js $jsHelper,
        DateTime $date,
        HelperFile $helperFile
    ) {
        $this->jsHelper    = $jsHelper;
        $this->date        = $date;
        $this->_helperFile = $helperFile;

        parent::__construct($fileFactory, $registry, $context);
    }

    /**
     * Save data action
     *
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws FileSystemException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPostValue()) {
            /** @var FileModel $file */
            $file = $this->initFile();
            if ($data['file']) {
                $this->_prepareData($file, $data['file']);
            }

            /** get file conditions */
            if (isset($data['rule'])) {
                $file->loadPost($data['rule']);
                $this->_eventManager->dispatch(
                    'mageplaza_productattachments_file_prepare_save',
                    ['post' => $file, 'request' => $this->getRequest()]
                );
            }
            if ($file->getData('error') && !$file->getData('file_path')) {
                $this->messageManager->addErrorMessage($file->getData('error'));

                if ($file->getId()) {
                    $resultRedirect->setPath(
                        'mpproductattachments/*/edit',
                        ['id' => $file->getId(), '_current' => true]
                    );

                    return $resultRedirect;
                }
            } else {
                try {
                    $file->save();

                    $this->messageManager->addSuccessMessage(__('The file has been saved.'));
                    $this->_getSession()->setData('mageplaza_productattachments_file_data', false);

                    if ($this->getRequest()->getParam('back')) {
                        $resultRedirect->setPath('mpproductattachments/*/edit', [
                            'id'       => $file->getId(),
                            '_current' => true
                        ]);
                    } else {
                        $resultRedirect->setPath('mpproductattachments/*/');
                    }

                    return $resultRedirect;
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (RuntimeException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (Exception $e) {
                    $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the File.'));
                }
            }

            $this->_getSession()->setData('mageplaza_productattachments_file_data', $data['file']);
            $resultRedirect->setPath('mpproductattachments/*/edit', ['id' => $file->getId(), '_current' => true]);

            return $resultRedirect;
        }
        $resultRedirect->setPath('mpproductattachments/*/');

        return $resultRedirect;
    }

    /**
     * @param FileModel $file
     * @param array $data
     *
     * @return $this
     * @throws FileSystemException
     */
    protected function _prepareData($file, $data = [])
    {
        if ($data['type'] === '0') {
            $this->_helperFile->uploadFile(
                $data,
                'file_path',
                HelperFile::TEMPLATE_MEDIA_TYPE_FILE,
                $file->getFilePath()
            );
        } else {
            $data['file_path']   = $data['file_link'];
            $data['file_action'] = 1;
        }
        if (!$file->getCreatedAt()) {
            $data['created_at'] = $this->date->date();
        }
        $data['file_icon_path'] = $data['icon'];
        $data['is_grid']        = 1;
        $file->addData($data);

        return $this;
    }
}
