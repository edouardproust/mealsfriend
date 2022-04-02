<?php namespace App\Model;

final class UserModel extends Model {

    protected $id;
    protected $created_at;
    protected $firstname;
    protected $lastname;
    private $username;
    private $password_hash;
    private $email;
    private $role;

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return "********";
    }

    public function getPasswordHash(): ?string
    {
        return $this->password_hash;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function getName(): ?string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

}