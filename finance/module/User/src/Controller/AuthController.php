<?php

namespace User\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Authentication\Result;
use Laminas\Uri\Uri;
use Laminas\View\Model\JsonModel;
use User\Entity\User;

/**
 * This controller is responsible for letting the user to log in and log out.
 */
class AuthController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Auth manager.
     * @var User\Service\AuthManager
     */
    private $authManager;

    /**
     * User manager.
     * @var User\Service\UserManager
     */
    private $userManager;

    /**
     * Constructor.
     */
    public function __construct($entityManager, $authManager, $userManager)
    {
        $this->entityManager = $entityManager;
        $this->authManager = $authManager;
        $this->userManager = $userManager;
    }

    public function authAction()
    {
        $email = $this->params()->fromPost('login', '');
        $password = $this->params()->fromPost('password', '');
        $rememberMe = $this->params()->fromPost('remember_me', 0);
        $token = $this->params()->fromPost('token', '');

        $response = [
            'result' => Result::FAILURE,
            'message' => ''
        ];

        try {
            $result = $this->authManager->login($email, $password, $rememberMe);

            // Check result.
            if ($result->getCode() == Result::SUCCESS) {
                $response['result'] = Result::SUCCESS;
                $response['redirect'] = $this->url()->fromRoute('home');
            } else {
                $response['message'] = join('<br/>', $result->getMessages());
            }

        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }

        return new JsonModel($response);
    }

    /**
     * Authenticates user given email address and password credentials.
     */
    public function loginAction()
    {
        // Retrieve the redirect URL (if passed). We will redirect the user to this
        // URL after successfull login.
        $redirectUrl = (string)$this->params()->fromQuery('redirectUrl', '');
        if (strlen($redirectUrl)>2048) {
            throw new \Exception("Too long redirectUrl argument passed");
        }

//        $redirectUrl = $this->params()->fromPost('redirect_url', '');
//
//        if (!empty($redirectUrl)) {
//            // The below check is to prevent possible redirect attack
//            // (if someone tries to redirect user to another domain).
//            $uri = new Uri($redirectUrl);
//            if (!$uri->isValid() || $uri->getHost()!=null)
//                throw new \Exception('Incorrect redirect URL: ' . $redirectUrl);
//        }
//
//        // If redirect URL is provided, redirect the user to that URL;
//        // otherwise redirect to Home page.
//        if(empty($redirectUrl)) {
//            return $this->redirect()->toRoute('home');
//        } else {
//            $this->redirect()->toUrl($redirectUrl);
//        }
    }

    /**
     * The "logout" action performs logout operation.
     */
    public function logoutAction()
    {
        $this->authManager->logout();

        return $this->redirect()->toRoute('login');
    }
}
