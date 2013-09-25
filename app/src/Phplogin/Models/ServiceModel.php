<?php

namespace Phplogin\Models;

use Phplogin\Models\UserStorageModel;
use PDO;

class ServiceModel
{
    /**
     * @var PDO
     */
    private $db;

    /**
     * @var UserStorageModel
     */
    private $userDao;

    public function __construct(PDO $database)
    {
        $this->db = $database;

        $this->userDao = new UserStorageModel($this->db);
    }

    public function getUserByName($username)
    {
        return $this->userDao->getUserByName($username);
    }
}
