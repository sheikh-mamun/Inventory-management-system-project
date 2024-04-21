<!DOCTYPE html>
<html>
<head>
        <title>Registration system PHP and MySQL</title>
        <link rel="stylesheet" href="css/style.css"> <!-- Link to your CSS file -->
</head>
<body>
<div class="header">
        <h2>Register</h2>
</div>
<form method="post" action="register.php">
        <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" value="">
        </div>
        <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" value="">
        </div>
        <div class="input-group">
                <label>Password</label>
                <input type="password" name="password_1">
        </div>
        <div class="input-group">
                <label>Confirm password</label>
                <input type="password" name="password_2">
        </div>
        <div class="input-group">
                <button type="submit" class="btn" name="register_btn">Register</button>
        </div>
        <p>
                Already a member? <a href="login.php">Sign in</a>
        </p>


        <?php 
session_start();

// connect to database
$db = mysqli_connect('localhost', 'root', '', 'multi_login');

// variable declaration
$username = "";
$email    = "";
$errors   = array(); 

// call the register() function if register_btn is clicked
if (isset($_POST['register_btn'])) {
        register();
}

// REGISTER USER
function register(){
        // call these variables with the global keyword to make them available in function
        global $db, $errors, $username, $email;

        // receive all input values from the form. Call the e() function
    // defined below to escape form values
        $username    =  e($_POST['username']);
        $email       =  e($_POST['email']);
        $password_1  =  e($_POST['password_1']);
        $password_2  =  e($_POST['password_2']);

        // form validation: ensure that the form is correctly filled
        if (empty($username)) { 
                array_push($errors, "Username is required"); 
        }
        if (empty($email)) { 
                array_push($errors, "Email is required"); 
        }
        if (empty($password_1)) { 
                array_push($errors, "Password is required"); 
        }
        if ($password_1 != $password_2) {
                array_push($errors, "The two passwords do not match");
        }

        // register user if there are no errors in the form
        if (count($errors) == 0) {
                $password = md5($password_1);//encrypt the password before saving in the database

                if (isset($_POST['user_type'])) {
                        $user_type = e($_POST['user_type']);
                        $query = "INSERT INTO users (username, email, user_type, password) 
                                          VALUES('$username', '$email', '$user_type', '$password')";
                        mysqli_query($db, $query);
                        $_SESSION['success']  = "New user successfully created!!";
                        header('location: home.php');
                }else{
                        $query = "INSERT INTO users (username, email, user_type, password) 
                                          VALUES('$username', '$email', 'user', '$password')";
                        mysqli_query($db, $query);

                        // get id of the created user
                        $logged_in_user_id = mysqli_insert_id($db);

                        $_SESSION['user'] = getUserById($logged_in_user_id); // put logged in user in session
                        $_SESSION['success']  = "You are now logged in";
                        header('location: index.php');                          
                }
        }
}

// return user array from their id
function getUserById($id){
        global $db;
        $query = "SELECT * FROM users WHERE id=" . $id;
        $result = mysqli_query($db, $query);

        $user = mysqli_fetch_assoc($result);
        return $user;
}

// escape string
function e($val){
        global $db;
        return mysqli_real_escape_string($db, trim($val));
}

function display_error() {
        global $errors;

        if (count($errors) > 0){
                echo '<div class="error">';
                        foreach ($errors as $error){
                                echo $error .'<br>';
                        }
                echo '</div>';
        }
}
?>       
</form>
</body>
</html>
