<?php
/**
 * Created by PhpStorm.
 * User: qingmeng-02
 * Date: 2018/1/25
 * Time: 14:57
 */

namespace limefamily\library;
use yii\base\InvalidConfigException;
use yii\web\Response;

use yii\web\ServerErrorHttpException;
use yii\base\Component;
use yii\httpclient\Client;
use Yii;


class LimeRights extends Component
{
    private $httpClient = null;

    public $baseUrl;
    public $systemId;
    public $systemSecret;

    private function handleError($response){
        if ($response instanceof \yii\httpclient\Response){
            $data = $response->getData();
        }else{
            throw new ServerErrorHttpException('权限服务器器错误');
        }

        if (array_key_exists('status', $data) && array_key_exists('type', $data) && array_key_exists('message', $data) && array_key_exists('code', $data)){
            if ($data['code'] == 101 || $data['code'] == 102){
                Yii::$app->cache->delete('rights_access_token');
            }
            throw  new $data['type']('来自权限服务器的错误:'.$data['message'], $data['code']);
        }else{
            throw new ServerErrorHttpException('权限服务器器错误');
        }
    }
    public function init(){
        parent::init();

        if ($this->baseUrl === null) {
            throw new InvalidConfigException('LimeRights::baseUrl must be set.');
        }
        if ($this->systemId === null) {
            throw new InvalidConfigException('LimeRights::systemId must be set.');
        }
        if ($this->systemSecret === null) {
            throw new InvalidConfigException('LimeRights::systemSecret must be set.');
        }
        $rights_access_token = Yii::$app->cache->get('rights_access_token');
        $this->httpClient = new Client(['baseUrl' => $this->baseUrl]);

        if (!isset($rights_access_token) || !$rights_access_token){
            $response = $this->httpClient->post('index.php?r=auth-system/token',['systemId' => $this->systemId, 'systemSecret' => $this->systemSecret])->send();
            if ($response->isOK && array_key_exists('token', $response->getData())){
                $rights_access_token = $response->getData()['token'];
                Yii::$app->cache->add('rights_access_token', $rights_access_token, 6600);
            }else{
                if (Yii::$app->id == 'app-frontend'){
                    Yii::$app->response->format = Response::FORMAT_JSON;
                }
                $this->handleError($response);
            }
        }
        $this->httpClient->requestConfig = [
            'headers' => [
                'Authorization' => 'Bearer '.$rights_access_token,
            ]
        ];

    }
    public function findIdentity($id){
        $response = $this->httpClient->get('index.php?r=user/find-identity-by-id',['id' => $id])->send();
        if($response->isOK && array_key_exists('user',$response->getData())){
            $userData = $response->getData()['user'];
            if ($userData){
                $user = new User();
                $user->setAttributes($userData, false);
                return $user;
            }else{
                return null;
            }
        }else{
            $this->handleError($response);
        }

    }
    public function findIdentityByAccessToken($token,$type){
        $response = $this->httpClient->get('index.php?r=user/find-identity-by-access-token',['token' => $token])->send();
        if($response->isOK && array_key_exists('user',$response->getData())){
            $userData = $response->getData()['user'];
            if ($userData){
                $user = new User();
                $user->setAttributes($userData, false);
                return $user;
            }else{
                return null;
            }
        }else{
            $this->handleError($response);
        }

    }
    public function findIdentityByUsername($username){
        $response = $this->httpClient->get('index.php?r=user/find-identity-by-username',['username' => $username])->send();
        if($response->isOK && array_key_exists('user', $response->getData())){
            $userData = $response->getData()['user'];
            if ($userData){
                $user = new User();
                $user->setAttributes($userData, false);
                return $user;
            }else {
                return null;
            }
        }else{
            $this->handleError($response);
        }

    }
    public function validatePassword($username, $password){
        $response = $this->httpClient->get('index.php?r=user/validate-password',['username' => $username, 'password' => $password])->send();
        if ($response->isOK && array_key_exists('result', $response->getData())){
            return $response->getData()['result'];
        }else {
            $this->handleError($response);
        }
    }
    public function login(){
        $response = $this->httpClient->post('index.php?r=site/login',Yii::$app->request->post())->send();
        if ($response->isOK){
            return $response->getData();
        }else{
            $this->handleError($response);
        }
    }
    public function logout(){
        $authHeader = Yii::$app->request->getHeaders()->get('Authorization');
        preg_match("/^Bearer\\s+(.*?)$/", $authHeader, $matches);
        $response = $this->httpClient
            ->post('index.php?r=site/logout', ['token' => $matches[1]])
            ->send();

        if ($response->isOK && array_key_exists('result', $response->getData())){
            return $response->getData();
        }else{
            $this->handleError($response);
        }

    }
    public function getProductsByUserId($userId){
        $response = $this->httpClient
            ->get('index.php?r=user/get-products', ['userId' => $userId])
            ->send();
        if ($response->isOK && array_key_exists('products', $response->getData())){
            return $response->getData()['products'];
        }else{
            $this->handleError($response);
        }
    }
    public function getRightsByUserId($userId){
        $response = $this->httpClient
            ->get('index.php?r=user/get-rights', ['userId' => $userId])
            ->send();
        if ($response->isOK && array_key_exists('rights', $response->getData())){
            return $response->getData()['rights'];
        }else{
            $this->handleError($response);
        }
    }
    public function getMenusByUserId($userId){
        $response = $this->httpClient
            ->get('index.php?r=user/get-menus', ['userId' => $userId])
            ->send();
        if ($response->isOK && array_key_exists('menus', $response->getData())){
            return $response->getData()['menus'];
        }else{
            $this->handleError($response);
        }
    }
    public function checkAccess($userId, $permissionName, $params){
        $response = $this->httpClient
            ->post('index.php?r=user/check-access', [
                'userId' => $userId,
                'permissionName' => $permissionName,
                'params' => $params
            ])
            ->setFormat(Client::FORMAT_JSON)
            ->send();
        if ($response->isOK && array_key_exists('result', $response->getData())){
            return $response->getData()['result'];
        }else{
            $this->handleError($response);
        }
    }
}