<?php

namespace User\Form;

use Zend\Form\Form;

class Login extends Form
{
  public function __construct($login = 'login') {

    parent::__construct($login);

    $this->setAttributes(array(
      'method' => 'post',
      'class' => 'col-md-6 first',
    ));
    $this->add(array(
      'name' => 'identity',
      'type' => 'Zend\Form\Element\Email',
      'attributes' => array(
        'id'   => 'email',
        // 'required' => 'required',
      ),
      'options' => array(
        'label' => 'Login <span>(Your email address)</span>',
        'wrapper_class' => 'user'
      ),
      'validators' => array( 
        array( 
          'name' => 'EmailAddress'
        ),
      ), 
    ));
    $this->add(array(
      'name' => 'credential',
      'type' => 'Zend\Form\Element\Password',
      'attributes' => array(
        'id'   => 'password',
        // 'required' => 'required',
      ),
      'options' => array(
        'label' => 'Password',
        'wrapper_class' => 'password'
      ),
    ));
  }
}