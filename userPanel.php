<?php
require_once "services/dbConfig.php";
require_once 'services/Course.php';
session_start();
mysqli_report(MYSQLI_REPORT_STRICT);
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'USER') {
    header('Location: index.php');
    exit();
}
try {
    $connection = new mysqli($host, $db_user, $db_password, $db_name);
    $connection->set_charset("utf8");
    if ($connection->connect_errno != 0) {
        throw new Exception(mysqli_connect_errno());
    } else {

        if (isset($_POST['password1'])) {
            $is_validated = true;
            $password1 = $_POST['password1'];
            $password2 = $_POST['password2'];

            if (strlen($password1) < 8 || strlen($password1) > 25) {
                $is_validated = false;
                $_SESSION['e_password'] = "Haslo musi zawierac od 8 do 25 znakow";
            }
            //passwords matching
            if ($password1 != $password2) {
                $is_validated = false;
                $_SESSION['e_password'] = "Hasla nie sa zgodne";
            }
            //hash password
            $password_hash = password_hash($password1, PASSWORD_DEFAULT);

            $_SESSION['fr_password1'] = $password1;
            $_SESSION['fr_password2'] = $password2;

            if ($is_validated) {
                if ($connection->query("UPDATE users SET users.password = '$password_hash'WHERE users.id = " . $_SESSION['userID'])) {
                    unset($_SESSION['fr_password1']);
                    unset($_SESSION['fr_password2']);
                    header('Location: services/logout.php');
                } else {
                    throw new Exception($connection->error);
                }
            }
        }
        if (isset($_POST['buyCourses'])) {
            foreach ($_SESSION['cart'] as $item) {
                $result = $connection->query('INSERT INTO cart VALUES (' . $_SESSION['userID'] . ',' . $item->id . ')');
                if (!$result) throw new Exception($connection->error);
            }
            unset($_SESSION['cart']);
            $_SESSION['succesfulBought'] = true;
        }
        $connection->close();
    }
} catch (Exception $e) {
    echo '<span style="color:red;">Blad serwera. Prosimy o rejestracje w innym terminie!</span>';
    echo '<br/>Informacja developerska:' . $e;
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--Bootstrap-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css" href="style/css/navbar.css">
    <link rel="stylesheet" type="text/css" href="style/css/footer.css">
    <link rel="stylesheet" type="text/css" href="style/css/panel.css">

</head>
<body>
<?php include 'navbar.php'; ?>
<div class="row">
    <div class="col-sm-3 col-lg-2 sidePanel">
        <div class="actions">
            <ol>
                <?php
                if (isset($_GET['action'])) {
                    $action = $_GET['action'];
                    if ($action == 'myProfile') {
                        ?>
                        <li><a href="userPanel.php?action=myProfile" style="color: coral;">Zmiana Hasła</a></li>
                        <li><a href="userPanel.php?action=cart">Moje zakupy</a></li>
                        <li><a href="userPanel.php?action=myCourses">Moje kursy</a></li>
                        <?php
                    }
                    if ($action == 'myCourses') {
                        ?>
                        <li><a href="userPanel.php?action=myProfile">Zmiana Hasla</a></li>
                        <li><a href="userPanel.php?action=cart">Moje zakupy</a></li>
                        <li><a href="userPanel.php?action=myCourses" style="color: coral;">Moje kursy</a></li>
                        <?php
                    }
                    if ($action == 'cart') {
                        ?>
                        <li><a href="userPanel.php?action=myProfile">Zmiana Hasla</a></li>
                        <li><a href="userPanel.php?action=cart" style="color: coral;">Moje zakupy</a></li>
                        <li><a href="userPanel.php?action=myCourses">Moje kursy</a></li>
                        <?php
                    }
                } else {
                    ?>
                    <li><a href="userPanel.php?action=myProfile">Zmiana Hasla</a></li>
                    <li><a href="userPanel.php?action=cart">Moje zakupy</a></li>
                    <li><a href="userPanel.php?action=myCourses" style="color: coral;">Moje kursy</a></li>
                    <?php
                }
                ?>

            </ol>
        </div>
    </div>
    <div class="col-sm-9 col-lg-10 contentContainer">
        <?php
        try {
            $connection = new mysqli($host, $db_user, $db_password, $db_name);
            if ($connection->connect_errno != 0) {
                throw new Exception(mysqli_connect_errno());
            } else {
                if (isset($_GET['action'])) {
                    $action = $_GET['action'];
                    if ($action == 'myProfile') {
                        $result = $connection->query("SELECT * FROM users WHERE users.id = " . $_SESSION['userID']);
                        if (!$result) throw new Exception($connection->error);
                        $row = $result->fetch_assoc();
                        ?>
                        <div>
                            <form method="post" id="changePassword">
                                <h2>Zmiana hasla:</h2>
                                </br>
                                <div class="form-group">
                                    <label for="newPassword">Nowe haslo:</label>
                                    <br/>
                                    <input class="form-control" id="newPassword" type="password" name="password1"
                                           placeholder="Haslo"
                                           value="<?php
                                           if (isset($_SESSION['fr_password1'])) {
                                               echo $_SESSION['fr_password1'];
                                               unset($_SESSION['fr_password1']);
                                           }
                                           ?>"/>
                                </div>
                                <br/>
                                <div class="form-group">
                                    <label for="newRePassword">Powtorz haslo:</label>
                                    <br/>
                                    <input class="form-control" id="newRePassword" type="password" name="password2"
                                           placeholder="Powtorz Haslo" value="<?php
                                    if (isset($_SESSION['fr_password2'])) {
                                        echo $_SESSION['fr_password2'];
                                        unset($_SESSION['fr_password2']);
                                    }
                                    ?>"/>
                                </div>
                                <div class="form-group">
                                    <?php
                                    if (isset($_SESSION['e_password'])) {
                                        echo '<div class="alert alert-danger" style="text-align: center">';
                                        echo $_SESSION['e_password'];
                                        echo '</div>';
                                        unset($_SESSION['e_password']);
                                    }
                                    ?>
                                </div>
                                <br/>
                                <button type="submit" class="btn btn-primary">Zapisz</button>
                            </form>
                        </div>
                        <?php
                    } elseif ($action == 'cart') {
                        $result = $connection->query("SELECT * FROM users");
                        if (!$result) throw new Exception($connection->error);
                        ?>
                        <h3>Moje zakupy:</h3>
                        <form method="post">
                            <table class="table table-hover">

                                <?php
                                if (isset($_SESSION['cart']) && sizeof($_SESSION['cart']) > 0) {
                                    ?>
                                    <thead>
                                    <tr>
                                        <th>Lp:</th>
                                        <th>Nazwa:</th>
                                        <th>Cena:</th>
                                        <th>Anuluj</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $licznik = 0;
                                    foreach ($_SESSION['cart'] as $item) {
                                        ?>
                                        <tr>
                                            <td><?php echo $licznik ?></td>
                                            <td><?php echo $item->name ?></td>
                                            <td><?php echo $item->price ?></td>
                                            <td>
                                                <button type="submit" value="<?php echo $item->id ?>"
                                                        class="deleteFromCart"
                                                        name="deleteFromCart">
                                                    &times;
                                                </button>
                                            </td>
                                        </tr>
                                        <?php
                                        $licznik++;
                                    }
                                    echo '</tbody>';
                                } elseif (isset($_SESSION['succesfulBought'])) {
                                    ?>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <div class="alert alert-success">
                                                Pomyślnie zakupiono wszystkie kursy!
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <?php
                                    unset($_SESSION['succesfulBought']);
                                } else {
                                    ?>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <div class="alert alert-info">
                                                Twoj koszyk jest pusty!
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <?php
                                }
                                ?>
                            </table>
                        </form>
                        <?php if (isset($_SESSION['cart']) && sizeof($_SESSION['cart']) > 0) { ?>
                            <div style="margin-left: 40%">
                                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"
                                      id="paypal" style="float: left">
                                    <input type="hidden" name="cmd" value="_s-xclick">
                                    <input type="hidden" name="hosted_button_id" value="9U8X2XTY6E2GY">
                                    <input type="image"
                                           src="https://www.paypalobjects.com/pl_PL/PL/i/btn/btn_buynow_LG.gif"
                                           border="0" name="submit" alt="PayPal – Płać wygodnie i bezpiecznie">
                                    <img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif"
                                         width="1" height="1">
                                </form>
                                <form method="post" style="float: left" id="testPayPal">
                                    <button type="submit" name="buyCourses">&nbsp;&nbsp;&nbsp;POZYSKAJ ZA DARMO&nbsp;&nbsp;&nbsp;</button>
                                </form>
                                <div style="clear:both;"></div>
                            </div>
                        <?php }
                    }
                }
                if (!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action'] == 'myCourses')) {
                    $result = $connection->query('SELECT * FROM courses INNER JOIN cart ON courses.id = cart.course_id JOIN users ON cart.user_id = users.id WHERE users.id = ' . $_SESSION['userID'] . ' ORDER BY courses.id');
                    if (!$result) throw new Exception($connection->error);
                    ?>
                    <h3>Moje kursy:</h3>
                    <form method="post">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Nazwa</th>
                                <th class="toHideInSmall">Opis</th>
                                <th>Kategoria</th>
                                <th>Przejdz do kursu</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $rows = $result->fetch_all(MYSQLI_NUM);
                            $start = 0;
                            if (isset($_GET['page'])) {
                                $start = 10 * ($_GET['page'] - 1);
                            }
                            $koniec = 9;
                            if (isset($_GET['page'])) {
                                $koniec = (10 * $_GET['page']) - 1;
                            }
                            if ($koniec > $result->num_rows) {
                                $koniec = $result->num_rows - 1;
                            }
                            for ($i = $start; $i <= $koniec; $i++) {
                                $row = $rows[$i];
                                ?>
                                <tr>
                                    <td><?php echo '<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/No_image_available_600_x_450.svg/1280px-No_image_available_600_x_450.svg.png" style="height: 40px">' ?></td>
                                    <td><?php echo $row[1] ?></td>
                                    <td class="toHideInSmall"><?php echo $row[2] ?></td>
                                    <td><?php echo $row[4] ?></td>
                                    <td>
                                        <?php
                                        echo '<a href="watch.php?video=tutajLinkDoMaterialuVideo"><button type="button" class="btn btn-primary">START</button></a>';
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </form>
                    <div class="pages">
                        <ul class="pagination">
                            <?php
                            for ($x = 1; $x <= ceil($result->num_rows / 10); $x++) {
                                if (isset($_GET['page'])) {
                                    if ($_GET['page'] == $x) {
                                        echo '<li class="active" ><a href="userPanel.php?action=myCourses&page=' . $x . '">' . $x . '</a></li>';
                                    } else {
                                        echo '<li><a href="userPanel.php?action=myCourses&page=' . $x . '">' . $x . '</a></li>';
                                    }
                                } else {
                                    if ($x == 1) {
                                        echo '<li class="active" ><a href="userPanel.php?action=myCourses&page=' . $x . '">' . $x . '</a></li>';
                                    } else {
                                        echo '<li><a href="userPanel.php?action=myCourses&page=' . $x . '">' . $x . '</a></li>';
                                    }
                                }
                            }
                            ?>
                        </ul>
                    </div>
                    <?php
                }
                $connection->close();
            }
        } catch (Exception $e) {
            echo '<span style="color:red;">Blad serwera. Prosimy o rejestracje w innym terminie!</span>';
            echo '<br/>Informacja developerska:' . $e;
        }
        ?>
    </div>
    <div style="clear: both;"></div>
</div>
<div class="flex-bottom">
    <?php include 'footer.php'; ?>
</div>

</body>
</html>
