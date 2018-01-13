<?php
if (isset($_GET['key'])) {
    require_once "services/dbConfig.php";
    mysqli_report(MYSQLI_REPORT_STRICT);
    try {
        $connection = new mysqli($host, $db_user, $db_password, $db_name);
        $connection->set_charset("utf8");
        if ($connection->connect_errno != 0) {
            throw new Exception(mysqli_connect_errno());
        } else {
            $result = $connection->query("UPDATE users SET users.isActive = 1 WHERE users.activation_link = '" . $_GET['key'] . "'");
            if (!$result) throw new Exception($connection->error);
            else {
                header('Location: successfulActivation.php');
                exit();
            }
            $connection->close();
        }
    } catch (Exception $e) {
        echo '<span style="color:red;">Blad serwera.</span>';
        echo '<br/>Informacja developerska:' . $e;
    }
} else {

}