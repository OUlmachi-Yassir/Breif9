<?php
  class User {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

    // Regsiter user
    public function register($data){
      $this->db->query('INSERT INTO utilisateurs (nom,prenom, email, pass) VALUES(:nom, :prenom, :email, :pass)');
      // Bind values
      $this->db->bind(':nom', $data['nom']);
      $this->db->bind(':prenom', $data['prenom']);
      $this->db->bind(':email', $data['email']);
      $this->db->bind(':pass', $data['pass']);

      // Execute
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }


    // Login User
    public function login($email, $password){
      $this->db->query('SELECT * FROM utilisateurs WHERE email = :email');
      $this->db->bind(':email', $email);

      $row = $this->db->single();

      $hashed_password = $row->pass;
      if(password_verify($password, $hashed_password)){
        return $row;
      } else {
        return false;
      }
    }

    // Find user by email
    public function findUserByEmail($email){
      $this->db->query('SELECT * FROM utilisateurs WHERE email = :email');
      // Bind value
      $this->db->bind(':email', $email);

      $row = $this->db->single();

      // Check row
      if($this->db->rowCount() > 0){
        return true;
      } else {
        return false;
      }
    }
  }