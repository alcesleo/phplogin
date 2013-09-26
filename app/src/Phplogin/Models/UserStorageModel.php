<?php

namespace Phplogin\Models;

use PDO;
use Phplogin\Exceptions\NotFoundException;

// TODO: Move this to a lib?
/**
 * Uses a SQLite3 database to access registered users
 */
class UserStorageModel
{
    /**
     * Holds the database-object
     * @var PDO object
     */
    private $pdo;

    /**
     * Table in the database that stores users
     * @var string
     */
    private static $tableName = 'User';

    /**
     * @param PDO $database
     */
    public function __construct(PDO $database)
    {
        $this->pdo = $database;
    }

    /**
     * Creates the table if it doesn't already exist.
     */
    private function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . self::$tableName . "
            (
                ID INTEGER PRIMARY KEY AUTOINCREMENT,
                UserName TEXT NOT NULL UNIQUE,
                Hash CHAR(40) NOT NULL
            )";

        $this->pdo->exec($sql);
    }


    /**
     * Get a user object by its username
     * @param  string $userName
     * @return UserModel
     */
    public function getByName($username)
    {
        // Prepare statment
        $sql = "SELECT * FROM " . self::$tableName . " WHERE UserName = :username";
        $stmt = $this->pdo->prepare($sql);

        // http://php.net/manual/en/pdostatement.bindparam.php
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        // Get data from db
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (! $result) {
            throw new NotFoundException('User not found');
        }

        return new UserModel($result['UserName'], $result['Hash'], $result['ID']);
    }

    /**
     * Get a user object by its ID
     * @param  int $userId
     * @return UserModel
     */
    public function getById($userId)
    {
        // Prepare statment
        $sql = "SELECT * FROM " . self::$tableName . " WHERE ID = :userid";
        $stmt = $this->pdo->prepare($sql);

        // http://php.net/manual/en/pdostatement.bindparam.php
        $stmt->bindParam(':userid', $userId, PDO::PARAM_INT);

        // Get data from pdo
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (! $result) {
            throw new NotFoundException('User not found');
        }

        return new UserModel($result['UserName'], $result['Hash'], $result['ID']);
    }

    /**
     * Insert a user into the database
     * @param  UserModel $user User to save
     * @return bool true on success, false on failure
     */
    public function insert(UserModel $user)
    {
        // TODO: Exception if user already exists

        // Prepare statement
        $sql = "INSERT INTO " . self::$tableName . " (UserName, Hash)
                VALUES (:username, :hash);";

        $stmt = $this->pdo->prepare($sql);

        // Bind values
        $stmt->bindValue(':username', $user->getUsername(), PDO::PARAM_STR);
        $stmt->bindValue(':hash', $user->getHash(), PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Update a user in the database
     * @param  UserModel $user The user to update
     * @return bool true on success, false on failure
     */
    public function update(UserModel $user)
    {
        // Prepare statement
        $sql = "UPDATE " . self::$tableName . "
                SET UserName=:username, Hash=:hash
                WHERE UserName;";

        $stmt = $this->pdo->prepare($sql);

        // Bind values
        $stmt->bindValue(':username', $user->getUsername(), PDO::PARAM_STR);
        $stmt->bindValue(':hash', $user->getHash(), PDO::PARAM_STR);

        return $stmt->execute();
    }
}
