<?php
namespace Vueai\ProductRecommendations\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Auth\Session;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Vueai\ProductRecommendations\Model\Signup;
use Vueai\ProductRecommendations\Helper\Data;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Vueai_ProductRecommendations::signup';
    /**
     * @var ClientFactory
     */
    private $httpClientFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Session
     */
    private $authSession;

    /**
     * @var Signup
     */
    private $sigupModel;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Category
     */
    private $category;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var Http
     */
    private $httpRequest;

    /**
     * @var Data
     */
    private $helper;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param ClientFactory $httpClientFactory
     * @param StoreManagerInterface $storeManager
     * @param Session $authSession
     * @param Signup $sigupModel
     * @param CategoryFactory $categoryFactory
     * @param ProductFactory $productFactory
     * @param CollectionFactory $collectionFactory
     * @param Category $category
     * @param Json $json
     * @param Http $httpRequest
     * @param Data $helper
     */
    public function __construct(
        Action\Context $context,
        ClientFactory $httpClientFactory,
        StoreManagerInterface $storeManager,
        Session $authSession,
        Signup $sigupModel,
        CategoryFactory $categoryFactory,
        ProductFactory $productFactory,
        CollectionFactory $collectionFactory,
        Category $category,
        Json $json,
        Http $httpRequest,
        Data $helper
    ) {
        $this->httpClientFactory = $httpClientFactory;
        $this->storeManager      = $storeManager;
        $this->authSession       = $authSession;
        $this->sigupModel        = $sigupModel;
        $this->categoryFactory   = $categoryFactory;
        $this->productFactory    = $productFactory;
        $this->collectionFactory = $collectionFactory;
        $this->category          = $category;
        $this->json              = $json;
        $this->httpRequest       = $httpRequest;
        $this->helper            = $helper;

        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $domain = $this->httpRequest->getServer('HTTP_HOST');
        $signupdata = $this->httpRequest->getPost();

        $data = [];
        foreach ($signupdata as $key => $row) {
            $data[$key] = $row;
        }
        $collection = [
                   'name'    => $data['name'],
                   'email'   => $data['email'],
                   'company' => $data['company'],
                   'phone'   => $data['phone'],
                   'region'  => $data['region'],
                   'domain'  => $domain,
                   'source'  => 'magento',
                   'event'   => 'signup'
        ];

        /**
         * var \Vueai\ProductRecommendations\Model\Signup $sigupModel
         */
        $model = $this->sigupModel->setData($collection);
        $model->setStoreId($data['store']);
        $apiUrl = $this->helper->getApiEndPoint();
        $client = $this->httpClientFactory->create();
        $client->post($apiUrl, $collection);
        $response = $this->json->unserialize($client->getBody());
        if (isset($response['message'])) {
            if ($response['message'] == "success") {
                $model->save();
                /**
                 * @var \Magento\Backend\Model\Auth\Session $authSession
                 */
                $this->authSession->setVueaiUserId($domain);
                $category = $this->getCategory();
                $product  = $this->getProduct($category->getId());

                $page_identifier = [
                    'source' => 'magento',
                    'domain' => $domain,
                    'company'=> $data['company'],
                    'email'  => $data['email'],
                    'event'  => 'pages',
                    'data'   =>
                        [
                            0 =>
                                [
                                    'type' => 'cat',
                                    'name' => 'Magento Category',
                                    'url'  => $category->getUrl(),
                                    'page_identifier' =>
                                        [
                                            'operation'  => 'and_conditions',
                                            'conditions' =>
                                                [
                                                    0 =>
                                                        [
                                                            'type'      => 'element',
                                                            'value'     => '#vue-ai-category-view',
                                                            'predicate' => 'contains',
                                                        ],
                                                ],
                                        ],
                                    'product_identifier' =>
                                        [
                                            'operation'  => 'and_conditions',
                                            'conditions' =>
                                                [
                                                    0 =>
                                                        [
                                                            'type'       => 'element',
                                                            'value'      => '#vue-ai-product',
                                                            'predicate'  => 'contains',
                                                            'extract_by' =>
                                                                [
                                                                    'value'     => '<product_id>',
                                                                    'attribute' => 'data-vue',
                                                                ],
                                                        ],
                                                ],
                                        ],
                                ],
                            1 =>
                                [
                                    'type' => 'pdp',
                                    'name' => 'Magento PDP',
                                    'url'  => $product->getProductUrl(),
                                    'page_identifier' =>
                                        [
                                            'operation'  => 'and_conditions',
                                            'conditions' =>
                                                [
                                                    0 =>
                                                        [
                                                            'type'      => 'element',
                                                            'value'     => '#vue-ai-product-page',
                                                            'predicate' => 'contains',
                                                        ],
                                                ],
                                        ],
                                    'product_identifier' =>
                                        [
                                            'operation' => 'and_conditions',
                                            'conditions' =>
                                                [
                                                    0 =>
                                                        [
                                                            'type'      => 'element',
                                                            'value'     => '#vue-ai-product-page',
                                                            'predicate' => 'contains',
                                                            'extract_by' =>
                                                                [
                                                                    'value'     => '<product_id>',
                                                                    'attribute' => 'data-vue',
                                                                ],
                                                        ],
                                                ],
                                        ],
                                    'uuid_identifier' =>
                                        [],
                                    'analytics' =>
                                        [
                                            'events' =>
                                                [
                                                    0 =>
                                                        [
                                                            'name'    => 'addToCart',
                                                            'action'  => 'click',
                                                            'element' => '#product-addtocart-button',
                                                        ],
                                                ],
                                        ],
                                    'language' => 1,
                                ],
                        ],
                ];
                $client->setOption(CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
                $client->post($apiUrl, $this->json->serialize($page_identifier));

                $response = $this->json->unserialize($client->getBody());
                if ($response['message'] == "success") {
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return  $resultRedirect->setPath('productrecommendations/index/success');
                } else {
                    $this->messageManager->addErrorMessage(
                        __('Invalid Page Data Should Be Passed')
                    );
                }
            } else {
                $this->messageManager->addErrorMessage(
                    __('Invalid SigningUp Data Should Be Passed')
                );
            }
        } else {
            $this->messageManager->addErrorMessage(
                __('Invalid SigningUp Data does not set')
            );
        }
    }

    /**
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCategory()
    {
        $category =  $this->collectionFactory->create()
            ->addAttributeToFilter('display_mode', ['in'=>["PRODUCTS", "PRODUCTS_AND_PAGE"]])
            ->addAttributeToFilter('level', ['gt'=> 1])
            ->setStore($this->storeManager->getStore())->getFirstItem();
        return $category;
    }

    /**
     * @param $categoryId
     * @return \Magento\Framework\DataObject
     */
    private function getProduct($categoryId)
    {
        return $this->categoryFactory->create()->load($categoryId)->getProductCollection()->getFirstItem();
    }
}
