<?php

namespace system\Repository;


use Database\PDODatabase;
use system\DTO\UserDTO;

class UserRepository
{
    private $db;

    public function __construct(PDODatabase $database)
    {
        $this->db = $database;
    }

    public function insert(UserDTO $user)
    {
        $stm = $this->db->query('INSERT INTO users (user_name, real_name, email, pass) VALUES 
                (:user_name, :real_name, :email, :pass)');

        $stm->execute([
            'usernmae' => $user->getUserName(),
            'real_name' => $user->getRealName(),
            'email' => $user->getPass(),
            'pass' => $user->getPass(),
        ]);

        return $this->db->lastInsertId();
    }

    public function getOne(int $user_id)
    {
        $stm = $this->db->query('SELECT * FROM `users` WHERE user_id = :user_id');
        $result = $stm->execute([
            'user_id' => $user_id
        ]);

        return $result->getOne(UserDTO::class);

    }

    public function check(string $username)
    {
        $stm = $this->db->query('SELECT * FROM `users` WHERE `user_name` = :username');

        $result = $stm->execute([
            'username' => $username
        ]);

        return $result->getOne(UserDTO::class);
    }

}