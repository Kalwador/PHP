<?php
if (isset($_GET['video'])) {
    require_once "services/dbConfig.php";
    mysqli_report(MYSQLI_REPORT_STRICT);
    try {
        $connection = new mysqli($host, $db_user, $db_password, $db_name);
        $connection->set_charset("utf8");
        if ($connection->connect_errno != 0) {
            throw new Exception(mysqli_connect_errno());
        } else {
            $result = $connection->query('SELECT * FROM courses where courses.id = ' . $_GET['video']);
            if (!$result) throw new Exception($connection->error);
            $video = $result->fetch_row();
            $connection->close();
        }
    } catch (Exception $e) {
        echo '<span style="color:red;">Blad serwera.</span>';
        echo '<br/>Informacja developerska:' . $e;
    }
} else {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style/css/watch.css">
    <link rel="stylesheet" type="text/css" href="style/css/navbar.css">
    <link rel="stylesheet" type="text/css" href="style/css/footer.css">

    <!--Bootstrap-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="embed-responsive embed-responsive-16by9">
        <iframe src="https://www.youtube.com/embed/<?php echo substr($video[5], 32); ?>" frameborder="0" allowfullscreen></iframe>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>