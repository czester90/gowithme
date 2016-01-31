<?php

namespace User\Controller;

use Zend\Form\Form;
use Zend\Crypt\Password\Bcrypt;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use User\Service\User as UserService;
use User\Options\UserControllerOptionsInterface;
use Application\Controller\BaseController;
use Zend\Stdlib\Parameters;

class UserController extends BaseController
{

    const ROUTE_LOGIN = 'user/login';
    const ROUTE_PROFILE = 'user/profile';
    const ROUTE_REGISTER = 'user/register';

    const CONTROLLER_NAME = 'user';


    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var Form
     */
    protected $loginForm;

    /**
     * @var Form
     */
    protected $registerForm;

    /**
     * @var Form
     */
    protected $changePasswordForm;

    /**
     * @var Form
     */
    protected $changeEmailForm;

    /**
     * @todo Make this dynamic / translation-friendly
     * @var string
     */
    protected $failedLoginMessage = 'Authentication failed. Please try again.';

    /**
     * @var UserControllerOptionsInterface
     */
    protected $options;
    protected $username;

    /**
     * Register new user
     */
    public function registerAction()
    {
        $request = $this->getRequest();
        $service = $this->getUserService();

        $post = $this->fromJson();
        $usernameExists = $this->em('User\Document\User')->findOneBy(['username' => $post['username']]);
        if ($usernameExists) {
            $this->errorResponse(['ERROR_USERNAME_EXISTS']);
        }

        $emailExists = $this->em('User\Document\User')->findOneBy(['email' => $post['email']]);
        if ($emailExists) {
            $this->errorResponse(['ERROR_EMAIL_EXISTS']);
        }
        $post['access_token'] = hash('sha256', 'test');
        $user = $service->register($post);

        if ($user) {
            $identityFields = $service->getOptions()->getAuthIdentityFields();
            if (in_array('email', $identityFields)) {
                $post['identity'] = $user->getEmail();
            } elseif (in_array('username', $identityFields)) {
                $post['identity'] = $user->getUsername();
            }
            $post['credential'] = $post['password'];
            $request->setPost(new Parameters($post));
            $this->authenticateAction();
        }

        return new JsonModel($user->toArray());
    }

    /**
     * Login form
     */
    public function loginAction()
    {
        if ($this->UserAuthentication()->getAuthService()->hasIdentity()) {

        }

        $request = $this->getRequest();
        $form = $this->getLoginForm();
        $values = $this->fromJson();
        $parameters = new Parameters();

        $parameters->set('identity', $values->identity);
        $parameters->set('credential', $values->credential);

        $request->setPost($parameters);
        $post = $request->getPost();

        $post->set('credential', trim($post->get('credential')));
        $form->setData($post);

        if (!$form->isValid()) {

        }

        // clear adapters
        $this->UserAuthentication()->getAuthAdapter()->resetAdapters();
        $this->UserAuthentication()->getAuthService()->clearIdentity();


        $adapter = $this->UserAuthentication()->getAuthAdapter();

        $adapter->prepareForAuthentication($this->getRequest());
        $auth = $this->UserAuthentication()->getAuthService()->authenticate($adapter);

        if (!$auth->isValid()) {

        }

        if($this->isUserLogin()) {
            $user = $this->em('User\Document\User')->find($this->getUserId());
            $user->setAccessToken(hash('sha256', $this->getUserId()));
            $this->em()->persist($user);
            $this->em()->flush();
        }

        return new JsonModel([
            'data' => 'ok'
        ]);

        /*$request = $this->getRequest();
        $form = $this->getLoginForm();

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $request->getQuery()->get('redirect')) {
            $redirect = $request->getQuery()->get('redirect');
        } else {
            $redirect = false;
        }

        if (!$request->isPost()) {
            return array(
                'loginForm' => $form,
                'redirect' => $redirect,
                'enableRegistration' => $this->getOptions()->getEnableRegistration(),
            );
        }

        $post = $request->getPost();

        $post->set('credential', trim($post->get('credential')));
        $form->setData($post);

        if (!$form->isValid()) {
            $this->flashMessenger()->addErrorMessage($this->failedLoginMessage);
            return $this->redirect()->toUrl($this->url()->fromRoute(static::ROUTE_LOGIN) . ($redirect ? '?redirect=' . $redirect : ''));
        }

        // clear adapters
        $this->UserAuthentication()->getAuthAdapter()->resetAdapters();
        $this->UserAuthentication()->getAuthService()->clearIdentity();


        return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate'));*/
    }

    /**
     * Logout and clear the identity
     */
    public function logoutAction()
    {

        $this->UserAuthentication()->getAuthAdapter()->resetAdapters();
        $this->UserAuthentication()->getAuthAdapter()->logoutAdapters();
        $this->getServiceLocator()->get('user_remember')->forgetMe();
        $this->UserAuthentication()->getAuthService()->clearIdentity();

        $sessionManager = new \Zend\Session\SessionManager();
        $sessionManager->forgetMe();

        $redirect = $this->params()->fromPost('redirect', $this->params()->fromQuery('redirect', false));

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $redirect) {
            return $this->redirect()->toUrl($redirect);
        }

        return $this->redirect()->toRoute($this->getOptions()->getLogoutRedirectRoute());
    }

    /**
     * General-purpose authentication action
     */
    public function authenticateAction()
    {
        $adapter = $this->UserAuthentication()->getAuthAdapter();
        $adapter->prepareForAuthentication($this->getRequest());

        $auth = $this->UserAuthentication()->getAuthService()->authenticate($adapter);

        if (!$auth->isValid()) {
            var_dump($auth->getCode());
            var_dump($auth->getMessages());
        }
    }

    public function changePasswordAction()
    {
        return new ViewModel(array());
    }

    /**
     * Logout and clear the identity
     */
    public function resetAction() {
        if($this->UserAuthentication()->getAuthService()->hasIdentity()) {
            return $this->redirect()->toRoute('user/profile');
        }

        $returnUri = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->url()->fromRoute('user/login');

        $request = $this->getRequest();
        $email = $this->params('email') ? $this->params('email') : $this->params()->fromPost('email');
        $user = $this->em('User\Entity\User')->findOneBy(array('email' => $email));

        if($request->getPost('email')) {
            if(!$user) {
                $this->flashMessenger()->addErrorMessage($email . ' nie istnieje w naszej bazie danych');
                return $this->redirect()->toUrl($this->url()->fromRoute('user/changepassword'));
            }

            $mail = new Message();
            $mail->setFrom('support@recmetals.com', 'RecMetals.com');
            $mail->addTo($user->getEmail(), $user->getFirstName() .' '. $user->getLastName());
            $mail->setEncoding('ISO-8859-2');

            $bcrypt = new Bcrypt();
            $bcrypt->setCost(14);

            $newPassword = $this->generatePassword();

            $user->setPassword($bcrypt->create($newPassword));
            $this->em()->persist($user);
            $this->em()->flush();

            $mail->setSubject('Nowe Hasło na RecMetals.com.');
            $mail->setBody("Witaj " .$user->getFirstName() . "!\n\n" .
                "Twoje hasło zostało zresetowane na Recmetals.com. Wygenerowaliśmy dla Ciebie nowe hasło:\n" .
                $newPassword . "\n Zmień hasło po zalogowaniu.\n\n" .
                "Zespół RecMetals.com!");

            $transport = new SmtpTransport();
            $options = new SmtpOptions(array(
                'host' => 'mymark.nazwa.pl',
                'connection_class' => 'login',
                'connection_config' => array(
                    'username' => 'support@mymark.nazwa.pl',
                    'password' => 'Qwedsazxc123'
                ),
                'port' => 25,
            ));
            $transport->setOptions($options);
            $transport->send($mail);

            $this->flashMessenger()->addSuccessMessage('Twoje nowe hasło zostało wysłane na Twoją skrzynkę pocztą. Prosze sprawdź skrzynkę pocztową.');
        } else {
            return $this->redirect()->toUrl($returnUri);
        }

        return $this->redirect()->toUrl($this->url()->fromRoute('user/login'));
    }

    public function getUserService()
    {
        if (!$this->userService) {
            $this->userService = $this->getServiceLocator()->get('user_user_service');
        }
        return $this->userService;
    }

    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
        return $this;
    }

    public function getRegisterForm()
    {
        if (!$this->registerForm) {
            $this->setRegisterForm($this->getServiceLocator()->get('user_register_form'));
        }
        return $this->registerForm;
    }

    public function setRegisterForm(Form $registerForm)
    {
        $this->registerForm = $registerForm;
    }

    public function getLoginForm()
    {
        if (!$this->loginForm) {
            $this->setLoginForm($this->getServiceLocator()->get('user_login_form'));
        }
        return $this->loginForm;
    }

    public function setLoginForm(Form $loginForm)
    {
        $this->loginForm = $loginForm;
        $fm = $this->flashMessenger()->setNamespace('user-login-form')->getMessages();
        if (isset($fm[0])) {
            $this->loginForm->setMessages(
                array('identity' => array($fm[0]))
            );
        }
        return $this;
    }

    public function setOptions(UserControllerOptionsInterface $options)
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions()
    {
        if (!$this->options instanceof UserControllerOptionsInterface) {
            $this->setOptions($this->getServiceLocator()->get('user_module_options'));
        }
        return $this->options;
    }
}
