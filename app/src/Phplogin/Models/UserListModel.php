<?php

namespace Phplogin\Models;

use PDO;
use Phplogin\Exceptions\NotFoundException;

class UserListModel
{
    /**
     * Holds the database-object
     * @var PDO object
     */
    private $db;

    /**
     * @param string $fileName Path to SQLite3 database file
     */
    public function __construct($fileName)
    {
        $this->initDatabase($fileName);
    }

    /**
     * Initializes connection to the database
     * @param  string $fileName path to SQLite3 database file
     */
    private function initDatabase($fileName)
    {
        // Connect to database
        $this->db = new PDO("sqlite:$fileName");

        // Set errormode to use php exceptions
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Make sure tables exist
        $this->createTable();
    }


    /**
     * Creates the table if it doesn't already exist.
     */
    private function createTable()
    {
        // FIXME: String dependancy
        $sql = "CREATE TABLE IF NOT EXISTS User (
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
    public function getUserByName($userName)
    {
        // FIXME: String dependancy
        // Prepare statment
        $sql = "SELECT * FROM User WHERE UserName = :username";
        $stmt = $this->db->prepare($sql);

        // http://php.net/manual/en/pdostatement.bindparam.php
        $stmt->bindParam(':username', $userName, PDO::PARAM_STR);

        // Get data from db
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (! $result) {
            throw new NotFoundException('User not found');
        }

        return new UserModel($result['UserName'], $result['Hash']);
    }

    /**
     * Insert a user into the database
     * @param  UserModel $user User to save
     */
    public function insertUser(UserModel $user)
    {
        // Prepare statement
        $sql = "INSERT INTO User (UserName, Hash) VALUES (:username, :hash);";
        $stmt = $this->db->prepare($sql);

        // Bind values
        $stmt->bindValue(':username', $user->getUsername(), PDO::PARAM_STR);
        $stmt->bindValue(':hash', $user->getHash(), PDO::PARAM_STR);

        $stmt->execute();
    }

    // FIXME: Testing code - remove
    public function getUserList()
    {
        $result = $this->db->query('SELECT * FROM User');
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}
