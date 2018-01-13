<?php
session_start();
if (!isset($_POST['login']) || !isset($_POST['password'])) {
    $_SESSION['login_error'] = '<span style="color:red">You bad boy!  ;)</span>';
    header('Location: index.php');
    exit();
}

require_once "dbConfig.php";

$connection = new mysqli($host, $db_user, $db_password, $db_name);

if ($connection->connect_errno != 0) {
    echo "Error:" . $connection->connect_errno . " Opis: " . $connection->connect_error;
} else {
    $login = $_POST['login'];
    $login = htmlentities($login, ENT_QUOTES, "UTF-8");
    $password = $_POST['password'];

    if ($resultSet = @$connection->query(
        sprintf("SELECT * FROM users WHERE username='%s'",
            mysqli_real_escape_string($connection, $login)))) {
        $foundUsers = $resultSet->num_rows;
        if ($foundUsers > 0) {
            $row = $resultSet->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                if($row['isActive']){
                    $_SESSION['isLogedIn'] = true;

                    $_SESSION['userID'] = $row['id'];
                    $_SESSION['user'] = $row['username'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['role'] = $row['role'];

                    unset($_SESSION['login_error']);
                    $resultSet->close();
                    header('Location: ../index.php');
                } else {
                    $_SESSION['login_error'] = 'Konto zablokowane!';
                    header('Location: ../index.php');
                }
            } else {
                $_SESSION['login_error'] = 'Nieprawidowy Login i/lub hasło!';
                header('Location: ../index.php');
            }
        } else {
            $_SESSION['login_error'] = 'Nieprawidowy Login i/lub hasło!';
            header('Location: ../index.php');
        }
    }

    $connection->close();
}
?>
