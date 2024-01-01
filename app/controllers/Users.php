<?php
  class Users extends Controller {
    public function __construct(){
      $this->userModel = $this->model('User');
    }

    public function register(){
      // Check for POST
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Process form
  
        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // Init data
        $data =[
          'nom' => trim($_POST['nom']),
          'prenom' => trim($_POST['prenom']),
          'email' => trim($_POST['email']),
          'pass' => trim($_POST['pass']),
          'confirm_password' => trim($_POST['confirm_password']),
          'nom_err' => '',
          'prenom_err' => '',
          'email_err' => '',
          'pass_err' => '',
          'confirm_password_err' => ''
        ];

        // Validate Email
        if(empty($data['email'])){
          $data['email_err'] = 'Pleae enter email';
        } else {
          // Check email
          if($this->userModel->findUserByEmail($data['email'])){
            $data['email_err'] = 'Email is already taken';
          }
        }

        // Validate Name
        if(empty($data['nom'])){
          $data['nom_err'] = 'Pleae enter name';
        }
        if(empty($data['prenom'])){
          $data['prenom_err'] = 'Pleae enter name';
        }

        // Validate Password
        if(empty($data['pass'])){
          $data['pass_err'] = 'Pleae enter password';
        } elseif(strlen($data['pass']) < 6){
          $data['pass_err'] = 'Password must be at least 6 characters';
        }

        // Validate Confirm Password
        if(empty($data['confirm_password'])){
          $data['confirm_password_err'] = 'Pleae confirm password';
        } else {
          if($data['pass'] != $data['confirm_password']){
            $data['confirm_password_err'] = 'Passwords do not match';
          }
        }

        // Make sure errors are empty
        if(empty($data['email_err']) && empty($data['nom_err']) && empty($data['prenom_err']) && empty($data['pass_err']) && empty($data['confirm_password_err'])){
          // Validated
          
          // Hash Password
          $data['pass'] = password_hash($data['pass'], PASSWORD_DEFAULT);

          // Register User
          if($this->userModel->register($data)){
            flash('register_success', 'You are registered and can log in');
            redirect('users/login');
          } else {
            die('Something went wrong');
          }

        } else {
          // Load view with errors
          $this->view('users/register', $data);
        }

      } else {
        // Init data
        $data =[
          'nom' => '',
          'prenom' => '',
          'email' => '',
          'pass' => '',
          'confirm_password' => '',
          'nom_err' => '',
          'prenom_err' => '',
          'email_err' => '',
          'pass_err' => '',
          'confirm_password_err' => ''
        ];

        // Load view
        $this->view('users/register', $data);
      }
    }

    public function login(){
      // Check for POST
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Process form
        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        
        // Init data
        $data =[
          'email' => trim($_POST['email']),
          'pass' => trim($_POST['pass']),
          'email_err' => '',
          'password_err' => '',      
        ];

        // Validate Email
        if(empty($data['email'])){
          $data['email_err'] = 'Pleae enter email';
        }

        // Validate Password
        if(empty($data['pass'])){
          $data['pass_err'] = 'Please enter password';
        }

        if($this->userModel->findUserByEmail($data['email'])){
          // User found
        } else {
          // User not found
          $data['email_err'] = 'No user found';
        }

        // Make sure errors are empty
        if(empty($data['email_err']) && empty($data['pass_err'])){
          // Validated
          // Check and set logged in user 
          $loggedInUser = $this->userModel->login($data['email'], $data['pass']);

          if($loggedInUser){
            // Create Session
            $this->createUserSession($loggedInUser);
          } else {
            $data['pass_err'] = 'Password incorrect';

          $this->view('users/login', $data);
        }
      } else {
        // Load view with errors
        $this->view('users/login', $data);
      }


      } else {
        // Init data
        $data =[    
          'email' => '',
          'pass' => '',
          'email_err' => '',
          'pass_err' => '',        
        ];

        // Load view
        $this->view('users/login', $data);
      }
    }

    public function createUserSession($user){
      $_SESSION['utilisateurs_id'] = $user->id;
      $_SESSION['utilisateurs_email'] = $user->email;
      $_SESSION['utilisateurs_nom'] = $user->nom;
      $_SESSION['utilisateurs_prenom'] = $user->prenom;
      redirect('pages/index');
    }

    public function logout(){
      unset($_SESSION['utilisateurs_id']);
      unset($_SESSION['utilisateurs_email']);
      unset($_SESSION['utilisateurs_nom']);
      unset($_SESSION['utilisateurs_prenom']);
      session_destroy();
      redirect('users/login');
    }

    public function isLoggedIn(){
      if(isset($_SESSION['utilisateurs_id'])){
        return true;
      } else {
        return false;
      }
    }
  }