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
        if (isset($_GET['category'])) {
            $result = $connection->query('SELECT * FROM courses WHERE courses.category = \'' . $_GET['category'] . '\'');
        } elseif (isset($_GET['search'])) {
            $result = $connection->query(
                'SELECT * FROM courses c WHERE c.description LIKE \'%' . $_GET['search'] . '%\' 
                    OR c.name LIKE \'%' . $_GET['search'] . '%\' 
                        OR c.category LIKE \'%' . $_GET['search'] . '%\'');
        } else {
            $result = $connection->query("SELECT * FROM courses ORDER BY RAND() LIMIT 12");
        }
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

        if (!$result) throw new Exception($connection->error);
        $courses = $result->fetch_all(MYSQLI_NUM);
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style/css/categories.css">
    <link rel="stylesheet" type="text/css" href="style/css/navbar.css">
    <link rel="stylesheet" type="text/css" href="style/css/footer.css">

    <!--Bootstrap-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="header header2">
    <div class="napisy">
        <?php
        if (isset($_GET['category'])) {
            echo '<h1>Pzeglad kursów</h1>';
            echo '<h3>Kategoria: ' . $_GET['category'] . '</h3>';
        } elseif (isset($_GET['search'])) {
            echo '<h1>Pzeglad kursów</h1>';
            echo '<h3>Slowa kluczowe: ' . $_GET['search'] . '</h3>';
        } else {
            echo '<h1>Pzeglad kursów</h1>';
        }
        ?>
    </div>
</div>


<div class="container">
    <div class="container2">
        <div class="row">
            <div class="nav nav-pills nav-stacked itemCategories">
                <span id="startOfCategories">Kategorie:</span>
                <a href="categories.php?category=HTML">HTML</a>
                <a href="categories.php?category=CSS">CSS</a>
                <a href="categories.php?category=PHP">PHP</a>
                <a href="categories.php?category=jQuery">jQuery</a>
                <a href="categories.php?category=Bootstrap">Bootstrap</a>
                <a href="categories.php?category=AJAX">AJAX</a>
                <a href="categories.php?category=Django">Django</a>
                <a href="categories.php?category=SpringBoot">SpringBoot</a>
                <a href="categories.php?category=ASP.NET">ASP .NET</a>
                <a href="categories.php?category=DropWizard">DropWizard</a>
            </div>
        </div>
        <div class="row">
            <div class="header">
                <div class="napisy">
                    <?php
                    if (isset($_GET['category'])) {
                        echo '<h1>Przeglad kursów</h1>';
                        echo '<h3>Kategoria: ' . $_GET['category'] . '</h3>';
                    } elseif (isset($_GET['search'])) {
                        echo '<h1>Pzeglad kursów</h1>';
                        echo '<h3>Slowa kluczowe: ' . $_GET['search'] . '</h3>';
                    } else {
                        echo '<h1>Pzeglad kursów</h1>';
                    }
                    ?>
                </div>
            </div>
            <div class="col-xs-8 col-xs-offset-2 col-md-9 col-md-offset-3 items">
                <?php
                $i = 0;
                foreach ($courses as $course) {
                    if ($i == 0) {
                        echo '<div class="row">';
                    }
                    $tempCourse = $courses[$i];
                    $price = $tempCourse[3] . 'ZL';
                    if ($price == 0) {
                        $price = 'FREE';
                    }
                    ?>
                    <form method="post" class="col-xs-6 col-md-4 col-lg-3 thumbshell">
                        <a href="<?php echo $tempCourse[5]; ?>">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/No_image_available_600_x_450.svg/1280px-No_image_available_600_x_450.svg.png">
                        </a>
                        <h4><?php echo $tempCourse[1]; ?></h4>
                        <p><?php echo substr($tempCourse[2], 0, 38); ?>...</p>
                        <?php
                        if (isset($_SESSION['user']) && $_SESSION['role'] == 'USER') {
                            ?>
                            <button type="submit" name="addToCart" value="<?php echo $tempCourse[0]; ?>"
                                    class="btn btn-primary">
                                <span class="glyphicon glyphicon-shopping-cart"
                                      style="float: left;padding-right: 3px;"></span>
                                <p class="price" style="float: left"> Cena: <span
                                            class="actualPrice"> <?php echo $price; ?></span></p>
                            </button>
                            <?php
                        }
                        ?>
                    </form>
                    <?php
                    $i++;
                }
                ?>
            </div>
        </div>
    </div>
</div>
</div>

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