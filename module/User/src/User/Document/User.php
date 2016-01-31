<?php

namespace User\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class User
{
    /**
     * Initialies the roles variable.
     */
    public function __construct()
    {
        $this->updated_at = new \MongoDate();
        $this->created_at = new \MongoDate();
    }

    /** @ODM\Id */
    private $id;

    /** @ODM\Field(type="string") @ODM\Index(unique=true, order="asc") */
    private $username;

    /** @ODM\Field(type="string") @ODM\Index(unique=true, order="asc") */
    private $email;

    /** @ODM\Field(type="string") */
    private $password;

    /** @ODM\Field(type="string") */
    private $password_recovery_hash;

    /** @ODM\Field(type="string") */
    private $access_token;

    /** @ODM\Field(type="date") */
    private $updated_at;

    /** @ODM\Field(type="date") */
    private $created_at;

    /**
     * @param mixed $access_token
     */
    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password_recovery_hash
     */
    public function setPasswordRecoveryHash($password_recovery_hash)
    {
        $this->password_recovery_hash = $password_recovery_hash;
    }

    /**
     * @return mixed
     */
    public function getPasswordRecoveryHash()
    {
        return $this->password_recovery_hash;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }


}