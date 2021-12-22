<?php
namespace Vueai\ProductRecommendations\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Auth\Session;
use Vueai\ProductRecommendations\Helper\Data;
use Vueai\ProductRecommendations\Model\SignupFactory;

class Signup extends \Magento\Backend\App\Action
{
    /**
     * @var Session
     */
    private $authSession;

    /**
     * @var SignupFactory
     */
    private $signupFactory;

    /**
     * @var Data
     */
    private $helper;

    /**
     * Signup constructor.
     * @param Action\Context $context
     * @param Session $authSession
     */
    public function __construct(
        Action\Context $context,
        Session $authSession,
        SignupFactory $signupFactory,
        Data $helper
    ) {
        $this->authSession      = $authSession;
        $this->signupFactory    = $signupFactory;
        $this->helper           = $helper;
        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $status = $this->helper->getStatusData();
        $storeId = $this->helper->getCompanyName()->getStoreId();
        if ($status->getStatus() == 1) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('productrecommendations/index/getstarted');
        } elseif ($status->getStatus() == 0 && $status->getStatus() != null) {
            $model = $this->signupFactory->create();
            $model->load($status->getId());
            $model->delete();
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } elseif ($storeId != null || $this->authSession->getVueaiUserId() != null) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('productrecommendations/index/success');
        } else {
            $this->authSession->setVueaiUserId(null);
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        }
    }
}
