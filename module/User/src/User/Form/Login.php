<?php

namespace User\Form;

use Zend\Form\Element;
use User\Form\ProvidesEventsForm;
use User\Options\AuthenticationOptionsInterface;

class Login extends ProvidesEventsForm
{
    /**
     * @var AuthenticationOptionsInterface
     */
    protected $authOptions;

    public function __construct($name = null, AuthenticationOptionsInterface $options)
    {
        $this->setAuthenticationOptions($options);
        parent::__construct($name);

        $this->add(array(
            'name' => 'identity',
            'attributes' => array(
                'type' => 'text',
                'class' => 'login_username',
                'placeholder' => 'Email'

            ),
        ));

        $this->add(array(
            'name' => 'credential',
            'attributes' => array(
                'type' => 'password',
                'class' => 'login_password',
                'placeholder' => 'Password'
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'remember',
            'options' => array(
                    'use_hidden_element' => true,
                    'checked_value' => '1',
                    'unchecked_value' => '0'
            )
        ));

        // @todo: Fix this
        // 1) getValidator() is a protected method
        // 2) i don't believe the login form is actually being validated by the login action
        // (but keep in mind we don't want to show invalid username vs invalid password or
        // anything like that, it should just say "login failed" without any additional info)
        //$csrf = new Element\Csrf('csrf');
        //$csrf->getValidator()->setTimeout($options->getLoginFormTimeout());
        //$this->add($csrf);

        $submitElement = new Element\Button('submit');
        $submitElement
            ->setLabel('Sign In')
            ->setAttributes(array(
                'type'  => 'submit',                
                'class' => 'login_submit formFooterButtons rounded Button primary Module large hasText btn',
                'value' => 'Zaloguj'
            ));

        $this->add($submitElement, array(
            'priority' => -100,
        ));

        $this->getEventManager()->trigger('init', $this);
    }

    /**
     * Set Authentication-related Options
     *
     * @param AuthenticationOptionsInterface $authOptions
     * @return Login
     */
    public function setAuthenticationOptions(AuthenticationOptionsInterface $authOptions)
    {
        $this->authOptions = $authOptions;
        return $this;
    }

    /**
     * Get Authentication-related Options
     *
     * @return AuthenticationOptionsInterface
     */
    public function getAuthenticationOptions()
    {
        return $this->authOptions;
    }
}
