<?php
    class UserModel {
        private $pdo;
    
        // Constructor: Initializes the model with a PDO connection
        public function __construct($pdo) {
            $this->pdo = $pdo;
        }
    
        // Function to get user details by username and verify password
        public function getUser($username, $password) {
            // Prepare SQL query to get the user by username
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();
    
            // Verify the provided password against the stored password hash
            if ($user && password_verify($password, $user['password'])) {
                return $user; // Returns user data if password is correct
            }
            
            return false; // Returns false if no match is found
        }
    }
    
    class LoginController {
        private $userModel;
    
        //Constructor: Initializes with the UserModel
        public function __construct($pdo) {
            $this->userModel = new UserModel($pdo);
        }
    
        //Login function to authenticate user and start session
        public function login($username, $password) {
            $user = $this->userModel->getUser($username, $password);
    
            if ($user) {
                //If user is found and password is correct, set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: musicle_home.php');
                exit();
            } else {
                //If login fails, set an error message and redirct to login page
                $_SESSION['error'] = "Invalid username or password";
                header('Location: login.php');
                exit();
            }
        }
    
        //Logout funtion to end the session
        public function logout() {
            session_unset();
            session_destroy();
            header('Location: login.php');
            exit();
        }
    }


    session_start();

    // Database connection
    $dsn='mysql:host=localhost:8889;dbname=musicle';
    $SQL_username='root';
    $SQL_password='root';

    try{
        $pdo= new PDO($dsn,$SQL_username,$SQL_password);
    }
    catch(PDOException $e){
        die('Connection error'.$e->getMessage());
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);


        $sql = "SELECT * FROM `users` WHERE username = '$username'";
        $results = ($pdo->query($sql))->fetchALL();

        if ($results) {
            $_SESSION['error'] = "Username already taken -- Please choose a different username";
            header('Location: views/register_view.php');
            exit();
        }
        else {
            $sql = "INSERT INTO `users`(`username`, `password`, `wins`, `losses`) VALUES ('$username','$hashed_password', 0, 0)";

            $pdo_statement=$pdo->prepare($sql);

            if(!$pdo_statement->execute()){
                echo "error ".$pdo_statement->error;
            }
            $loginController = new LoginController($pdo);
            $loginController->login($username, $password);
        }
    

        
        
    }

?>