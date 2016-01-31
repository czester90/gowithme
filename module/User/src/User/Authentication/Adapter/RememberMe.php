<?php

namespace User\Authentication\Adapter;
 
use Zend\Authentication\Storage;
 
class RememberMe extends Storage\Session
{
    public function setRememberMe($rememberMe = 0, $time = 31536000)
    {
         if ($rememberMe == 1) {
             $this->session->getManager()->rememberMe($time);
         }
    }
     
    public function forgetMe()
    {
        $this->session->getManager()->forgetMe();
    }
}