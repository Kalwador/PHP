<?php
session_start();
require_once "dbConfig.php";
mysqli_report(MYSQLI_REPORT_STRICT);
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'ADMIN') {
    header('Location: index.php');
    exit();
}
try {
    $connection = new mysqli($host, $db_user, $db_password, $db_name);
    if ($connection->connect_errno != 0) {
        throw new Exception(mysqli_connect_errno());
    } else {
        if ($connection->query('UPDATE courses c SET c.name = \'' . $_POST['name'] . '\' 
                                    , c.description = \'' . $_POST['description'] . '\' 
                                    , c.price = \'' . $_POST['price'] . '\' 
                                    , c.category = \'' . $_POST['category'] . '\' WHERE c.id = \''.$_POST['id'] .'\'')) {
            $connection->close();
            header('Location: ../adminPanel.php');
        } else {
            throw new Exception($connection->error);
        }


    }
} catch (Exception $e) {
    echo '<span style="color:red;">Blad serwera. Prosimy o rejestracje w innym terminie!</span>';
    echo '<br/>Informacja developerska:' . $e;
}
?>

