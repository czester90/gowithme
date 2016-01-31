<?php

namespace Application\Controller;

use Application\Components\Response\HttpResponse;
use User\Controller\UserController;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class BaseController extends AbstractActionController
{
    public $request;
    public $param_id;
    public $session;

    public function __construct()
    {
        $this->request = $this->getRequest();
    }

    public function getParam($param)
    {
        $paramValue = $this->params()->fromRoute($param);
        return $paramValue ? $paramValue : null;
    }

    public function em($namespace = null, $database = 'default')
    {
        if ($namespace) {
            return $this->getServiceLocator()->get('doctrine.documentmanager.odm_' . $database)->getRepository($namespace);
        } else {
            return $this->getServiceLocator()->get('doctrine.documentmanager.odm_' . $database);
        }
    }

    public function jsonResponse(array $param)
    {
        return new JsonModel(array(
            'param' => $param,
            'success' => true,
        ));
    }

    public function user()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('user_auth_service');
    }

    public function fromJson() {
        $body = $this->request->getContent();
        if (!empty($body)) {
            $json = json_decode($body, true);
            if (!empty($json)) {
                return $json;
            }
        }

        return [];
    }

    public function getUserId()
    {
        return $this->user()->getIdentity()->getId();
    }

    public function getCompanyId()
    {
        return $this->user()->getIdentity()->getCompanyId();
    }

    public function post($name)
    {
        return $this->request->getPost($name);
    }

    public function files($name)
    {
        return $this->request->getFiles($name);
    }

    public function isUser()
    {
        if (!$this->UserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(UserController::ROUTE_LOGIN);
        }

        return false;
    }

    public function isUserLogin()
    {
        return $this->UserAuthentication()->hasIdentity();
    }

    public function getHttpResponse()
    {
        return new HttpResponse();
    }

    public function successResponse($data)
    {
        $this->getHttpResponse()->successResponse($data);
    }

    public function errorResponse($data, $status = HttpResponse::STATUS_NOT_FOUND)
    {
        $this->getHttpResponse()->errorResponse($data, $status);
    }
}
