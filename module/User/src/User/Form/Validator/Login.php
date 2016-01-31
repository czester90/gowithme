<?php 

namespace User\Form\Validator; 

use Zend\InputFilter\Factory as InputFactory; 
use Zend\InputFilter\InputFilter; 
use Zend\InputFilter\InputFilterAwareInterface; 
use Zend\InputFilter\InputFilterInterface; 

class Login implements InputFilterAwareInterface { 

  protected $inputFilter; 

  public function setInputFilter(InputFilterInterface $inputFilter) { 
    throw new \Exception("Not used");
  }

  public function getInputFilter() {

    if (!$this->inputFilter) {
      $inputFilter = new InputFilter(); 
      $factory     = new InputFactory(); 
    }
    
    $inputFilter->add($factory->createInput(array(
      'name' => 'identity',
      'filters' => array( 
        array('name' => 'StripTags'),
        array('name' => 'StringTrim'),
      ),
      'validators' => array(
        array(
          'name' => 'EmailAddress',
          'options' => array(
            'messages' => array(
              'emailAddressInvalidFormat' => 'Email address format is invalid',
            ),
          ),
        ),
        array(
          'name' => 'NotEmpty', 
          'options' => array(
            'messages' => array(
              'isEmpty' => 'Email address is required', 
            )
          ),
        ),
      ),
    ))); 

    $inputFilter->add($factory->createInput(array(
      'name' => 'credential', 
      'filters' => array( 
        array('name' => 'StripTags'),
        array('name' => 'StringTrim'),
      ), 
      'validators' => array(
        array(
          'name' => 'NotEmpty', 
          'options' => array( 
            'messages' => array( 
              'isEmpty' => 'Password is required', 
            ),
          ),
        ),
      ), 
    )));
    return $inputFilter;
  }
}