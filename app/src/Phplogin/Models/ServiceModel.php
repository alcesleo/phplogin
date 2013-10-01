<?php

namespace Phplogin\Models;

use Phplogin\Models\TemporaryPasswordModel;
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
    private $userStorage;

    public function __construct(PDO $database)
    {
        $this->db = $database;

        $this->userStorage = new UserStorageModel($this->db);
        $this->tempStorage = new TemporaryPasswordStorageModel($this->db);
    }

    /**
     * @param  string $username
     * @return UserModel
     */
    public function getUserByName($username)
    {
        return $this->userStorage->getByName($username);
    }

    /**
     * @param  int $userId
     * @return UserModel
     */
    public function getUserById($userId)
    {
        return $this->userStorage->getById($userId);
    }

    /**
     * @param  int $temppwId
     * @return TemporaryPasswordModel
     */
    public function getTemporaryPasswordById($temppwId)
    {
        return $this->tempStorage->getById($temppwId);
    }

    /**
     * Inserts or updates the passed in temporary password
     * @param  TemporaryPasswordModel $temppw
     * @return bool                           true on success, false on failure
     */
    public function saveTemporaryPassword(TemporaryPasswordModel $temppw)
    {
        if ($this->tempStorage->idExists($temppw->getUserId())) {
            return $this->tempStorage->update($temppw);
        }
        return $this->tempStorage->insert($temppw);
    }

    /**
     * @param int $userid
     * @return bool
     */
    public function deleteTemporaryPassword($userid)
    {
        $this->tempStorage->deleteByUser($userid);
    }
}
