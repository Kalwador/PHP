<?php
require_once 'services/Course.php';
session_start();
require_once "services/dbConfig.php";
mysqli_report(MYSQLI_REPORT_STRICT);
try {
    $connection = new mysqli($host, $db_user, $db_password, $db_name);
    $connection->set_charset("utf8");
    if ($connection->connect_errno != 0) {
        throw new Exception(mysqli_connect_errno());
    } else {
        $result = $connection->query("SELECT * FROM courses ORDER BY RAND() LIMIT 12");
        if (!$result) throw new Exception($connection->error);
        $randomCourses = $result->fetch_all(MYSQLI_NUM);
        if (isset($_POST['addToCart'])) {
            if (isset($_SESSION['cart'])) {
                $cart = $_SESSION['cart'];
            } else {
                $cart = array();
            }

            $resultToAdd = $connection->query("SELECT * FROM courses WHERE courses.id = " . $_POST['addToCart']);
            if (!$resultToAdd) throw new Exception($connection->error);
            $courseToAdd = $resultToAdd->fetch_row();

            $tempCourse = new Course();
            $tempCourse->id = $courseToAdd[0];
            $tempCourse->name = $courseToAdd[1];
            $tempCourse->price = $courseToAdd[3];
            $cart[$_POST['addToCart']] = $tempCourse;
            $_SESSION['cart'] = $cart;
            unset($_POST['addToCart']);
        }
        $connection->close();
    }
} catch (Exception $e) {
    echo '<span style="color:red;">Blad serwera.</span>';
    echo '<br/>Informacja developerska:' . $e;
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="style/css/bootstrap.min.css">
    <script src="style/js/jquery-3.2.1.min.js"></script>
    <script src="style/js/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css" href="style/css/navbar.css">
    <link rel="stylesheet" type="text/css" href="style/css/footer.css">
    <link rel="stylesheet" type="text/css" href="style/css/index.css">

</head>
<body>
<?php include 'navbar.php'; ?>
<div class="banner">
    <img src="media/baner-test2.png">
</div>
<section class="container text-center gridContainer">
    <form class="row" method="post">
        <h3>Kursy tworzenia witryn internetowych</h3>
        <?php
        for ($i = 0; $i < 4; $i++) {
            $randomCourse = $randomCourses[$i];
            $price = $randomCourse[3] . 'ZL';
            if ($price == 0) {
                $price = 'FREE';
            }
            ?>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 thumbshell">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/No_image_available_600_x_450.svg/1280px-No_image_available_600_x_450.svg.png" alt="image">
                <h4><?php echo $randomCourse[1]; ?></h4>
                <p><?php echo $randomCourse[2]; ?></p>
                <?php
                if (isset($_SESSION['user']) && $_SESSION['role'] == 'USER') {
                    ?>
                    <button type="submit" name="addToCart" value="<?php echo $randomCourse[0]; ?>"
                            class="btn btn-primary">
                        <span class="glyphicon glyphicon-shopping-cart" style="float: left;padding-right: 3px;"></span>
                        <p class="price" style="float: left"> Cena: <span
                                    class="actualPrice"><?php echo $price; ?></span></p>
                    </button>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>
    </form>
    <br/>
    <form class="row" method="post">
        <h3>Mechanika stron webowych</h3>
        <?php
        for ($i = 4; $i < 8; $i++) {
            $randomCourse = $randomCourses[$i];
            $price = $randomCourse[3] . 'ZL';
            if ($price == 0) {
                $price = 'FREE';
            }
            ?>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 thumbshell">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/No_image_available_600_x_450.svg/1280px-No_image_available_600_x_450.svg.png" alt="image">
                <h4><?php echo $randomCourse[1]; ?></h4>
                <p><?php echo $randomCourse[2]; ?></p>
                <?php
                if (isset($_SESSION['user']) && $_SESSION['role'] == 'USER') {
                    ?>
                    <button type="submit" name="addToCart" value="<?php echo $randomCourse[0]; ?>"
                            class="btn btn-primary">
                        <span class="glyphicon glyphicon-shopping-cart" style="float: left;padding-right: 3px;"></span>
                        <p class="price" style="float: left"> Cena: <span
                                    class="actualPrice"> <?php echo $price; ?></span></p>
                    </button>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>
    </form>
    <br/>
    <form class="row" method="post">
        <h3>Responsywne witryny internetowe</h3>
        <?php
        for ($i = 8; $i < 12; $i++) {
            $randomCourse = $randomCourses[$i];
            $price = $randomCourse[3] . 'ZL';
            if ($price == 0) {
                $price = 'FREE';
            }
            ?>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 thumbshell">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/No_image_available_600_x_450.svg/1280px-No_image_available_600_x_450.svg.png" alt="image">
                <h4><?php echo $randomCourse[1]; ?></h4>
                <p><?php echo $randomCourse[2]; ?></p>
                <?php
                if (isset($_SESSION['user']) && $_SESSION['role'] == 'USER') {
                    ?>
                    <button type="submit" name="addToCart" value="<?php echo $randomCourse[0]; ?>"
                            class="btn btn-primary">
                        <span class="glyphicon glyphicon-shopping-cart" style="float: left;padding-right: 3px;"></span>
                        <p class="price" style="float: left"> Cena: <span
                                    class="actualPrice"> <?php echo $price; ?></span></p>
                    </button>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>
    </form>
</section>
<br/><br/>

<?php include 'footer.php'; ?>

<!--Skrypt do modala-->
<script>
    var modal = document.getElementById('loginModal');
    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };
    var cartModal = document.getElementById('cartModal');
    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };
</script>
</body>
</html>