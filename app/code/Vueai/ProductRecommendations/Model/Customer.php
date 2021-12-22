<?php

namespace Vueai\ProductRecommendations\Model;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Serialize\Serializer\Json;
use Vueai\ProductRecommendations\Api\CustomertInterface;
use Vueai\ProductRecommendations\Helper\Data;
use Vueai\ProductRecommendations\Model\Signup;

class Customer implements CustomertInterface
{
    /**
     * @var Json
     */
    private $json;

    /**
     * @var Session
     */
    private $authSession;

    /**
     * @var Signup
     */
    private $sigupModel;

    /**
     * @var Data
     */
    private $helper;

    /**
     * Customer constructor.
     *
     * @param Json $json
     * @param Signup $signupModel
     * @param Session $authSession
     * @param Data $helper
     */
    public function __construct(
        Json $json,
        Signup $signupModel,
        Session $authSession,
        Data $helper
    ) {
        $this->json        = $json;
        $this->sigupModel  = $signupModel;
        $this->authSession = $authSession;
        $this->helper      = $helper;
    }

    /**
     * Returns success response
     *
     * @api
     * @param string $domainName
     * @param string $email
     * @param string $status
     * @param string $embededCode
     * @param string $apiUrl
     * @return bool|string
     * @throws \Exception
     */
    public function getVueaiData(
        $domainName,
        $email,
        $status,
        $embededCode,
        $apiUrl
    ) {
            $collection = [
                'domain'        => $domainName ,
                'email'         => $email ,
                'status'        => $status ,
                'embedded_code' => $embededCode,
                'api_url'       => $apiUrl
            ];
            $model = $this->sigupModel->load($domainName, 'domain');
            if ($model->getId()) {
                $model->setDomain($domainName);
                $model->setEmail($email);
                $model->setStatus($status);
                $model->setEmbeddedCode($embededCode);
                $model->setApiUrl($apiUrl);
            } else {
                $model = $this->sigupModel->setData($collection);
                $model->setStoreId($this->helper->getCurrentStoreId());
            }
            $model->save();
            $response['message'] = 'success';
            $response['status'] = 1;
            return $this->json->serialize($response);
    }
}
