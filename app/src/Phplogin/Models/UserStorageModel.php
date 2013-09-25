<?php

namespace Phplogin\Models;

use PDO;
use Phplogin\Exceptions\NotFoundException;

// TODO: Move this to a lib?
/**
 * Uses a SQLite3 database to access registered users
 */
class UserListModel
{
    /**
     * Holds the database-object
     * @var PDO object
     */
    private $db;

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
        $this->db = $database;
        $this->createTable();
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

        $this->db->exec($sql);
    }


    /**
     * Get a user object by its username
     * @param  string $userName
     * @return UserModel
     */
    public function getUserByName($username)
    {
        // Prepare statment
        $sql = "SELECT * FROM " . self::$tableName . " WHERE UserName = :username";
        $stmt = $this->db->prepare($sql);

        // http://php.net/manual/en/pdostatement.bindparam.php
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        // TODO: Look up fetch object
        // Get data from db
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (! $result) {
            throw new NotFoundException('User not found');
        }

        return new UserModel($result['UserName'], $result['Hash']);
    }

    // TODO: Throws?
    /**
     * Insert a user into the database
     * @param  UserModel $user User to save
     */
    public function insertUser(UserModel $user)
    {
        // TODO: Exception if user already exists

        // Prepare statement
        $sql = "INSERT INTO " . self::$tableName . " (UserName, Hash)
                VALUES (:username, :hash);";

        $stmt = $this->db->prepare($sql);

        // Bind values
        $stmt->bindValue(':username', $user->getUsername(), PDO::PARAM_STR);
        $stmt->bindValue(':hash', $user->getHash(), PDO::PARAM_STR);

        $stmt->execute();
    }

    /**
     * TODO: Finish this function, private for now
     * Update a user in the database
     * @param  UserModel $user The user to update
     * @return [type]          [description]
     */
    private function updateUser(UserModel $user)
    {
        // Prepare statement
        $sql = "UPDATE " . self::$tableName . "
                SET UserName=:username, Hash=:hash
                WHERE UserName;";

        $stmt = $this->db->prepare($sql);

        // Bind values
        $stmt->bindValue(':username', $user->getUsername(), PDO::PARAM_STR);
        $stmt->bindValue(':hash', $user->getHash(), PDO::PARAM_STR);

        $stmt->execute();
    }
}
