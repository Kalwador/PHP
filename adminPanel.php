<?php
session_start();
require_once "services/dbConfig.php";
mysqli_report(MYSQLI_REPORT_STRICT);
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'ADMIN') {
    header('Location: index.php');
    exit();
}
try {
    $connection = new mysqli($host, $db_user, $db_password, $db_name);
    $connection->set_charset("utf8");
    if ($connection->connect_errno != 0) {
        throw new Exception(mysqli_connect_errno());
    } else {
        if (isset($_POST['deleteUser'])) {
            $connection->query('DELETE FROM users WHERE id = ' . $_POST['deleteUser']);
            unset($_POST['delete']);
        }
        if (isset($_POST['suspend'])) {
            $connection->query('UPDATE users SET isActive = 0 WHERE id = ' . $_POST['suspend']);
            unset($_POST['suspend']);
        }
        if (isset($_POST['unsuspend'])) {
            $connection->query('UPDATE users SET isActive = 1 WHERE id = ' . $_POST['unsuspend']);
            unset($_POST['unsuspend']);
        }
        if (isset($_POST['name'])) {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $category = $_POST['category'];
            $link = $_POST['link'];
            if ($connection->query("INSERT INTO courses VALUES (NULL, '$name','$description','$price','$category','$link')")) {
                $_SESSION['succesful_add new course'] = true;
            } else {
                throw new Exception($connection->error);
            }
        }
        if (isset($_POST['editCourse'])) {
            $resultToEdit = $connection->query("SELECT * FROM courses WHERE courses.id = " . $_POST['editCourse']);
            if (!$resultToEdit) throw new Exception($connection->error);
            $editedCourse = $resultToEdit->fetch_row();
        }
        if (isset($_POST['deleteCourse'])) {
            $connection->query('DELETE FROM courses WHERE id = ' . $_POST['deleteCourse']);
            unset($_POST['deleteCourse']);
        }
        $connection->close();
    }
} catch (Exception $e) {
//    echo '<span style="color:red;">Blad serwera.!</span>';
//    echo '<br/>Informacja developerska:' . $e;
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
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
                    if ($action == 'allCourses') {
                        ?>
                        <li><a href="adminPanel.php?action=allCourses" style="color: coral;">Lista kursów</a></li>
                        <li><a href="adminPanel.php?action=addNewCourse">Dodaj nowy kurs</a></li>
                        <li><a href="adminPanel.php?action=allUsers">Lista użytkowników</a></li>
                        <?php
                    }
                    if ($action == 'addNewCourse') {
                        ?>
                        <li><a href="adminPanel.php?action=allCourses">Lista kursów</a></li>
                        <li><a href="adminPanel.php?action=addNewCourse" style="color: coral;">Dodaj nowy kurs</a></li>
                        <li><a href="adminPanel.php?action=allUsers">Lista użytkowników</a></li>
                        <?php
                    }
                    if ($action == 'allUsers') {
                        ?>
                        <li><a href="adminPanel.php?action=allCourses">Lista kursów</a></li>
                        <li><a href="adminPanel.php?action=addNewCourse">Dodaj nowy kurs</a></li>
                        <li><a href="adminPanel.php?action=allUsers" style="color: coral;">Lista użytkowników</a></li>
                        <?php
                    }
                } else {
                    ?>
                    <li><a href="adminPanel.php?action=allCourses" style="color: coral;">Lista kursów</a></li>
                    <li><a href="adminPanel.php?action=addNewCourse">Dodaj nowy kurs</a></li>
                    <li><a href="adminPanel.php?action=allUsers">Lista użytkowników</a></li>
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
                    if ($action == 'addNewCourse') {
                        ?>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <form method="post" id="addNewCourse">
                                <div class="frame">
                                    <h3>Dodaj nowy kurs:</h3>
                                    <input type="text" class="form-control" name="name" placeholder="Nazwa..">
                                    </br>
                                    <textarea class="form-control" rows="5" name="description"
                                              placeholder="Opis kursu.."></textarea>
                                    </br>
                                    <input type="text" class="form-control" name="price" placeholder="Cena kursu..">
                                    </br>
                                    <select class="form-control" name="category">
                                        <option>HTML</option>
                                        <option>CSS</option>
                                        <option>PHP</option>
                                        <option>jQuery</option>
                                        <option>Bootstrap</option>
                                        <option>AJAX</option>
                                        <option>Django</option>
                                        <option>SpringBoot</option>
                                        <option>ASP .NET</option>
                                        <option>DropWizard</option>
                                    </select>
                                    </br>
                                    <input type="text" class="form-control" name="link" placeholder="Link do kursu..">
                                    </br>
                                    <button type="submit" class="btn btn-primary">Dodaj Kurs</button>
                                </div>
                            </form>
                        </div>
                        <?php
                    } elseif ($action == 'allUsers') {
                        $result = $connection->query("SELECT * FROM users");
                        if (!$result) throw new Exception($connection->error);
                        $rows = $result->fetch_all(MYSQLI_NUM);
                        ?>
                        <h3>Lista uzytkownikow:</h3>
                        <form method="post">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Username</th>
                                    <th class="toHideInSmall">Email</th>
                                    <th>Rola</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php

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
                                        <td><?php echo $row[0] ?></td>
                                        <td><?php echo $row[1] ?></td>
                                        <td class="toHideInSmall"><?php echo $row[3] ?></td>
                                        <td><?php echo $row[4] ?></td>
                                        <td>
                                            <?php
                                            if ($row[5] == 1) {
                                                echo '<button type="submit" name="suspend" value="' . $row[0] . '" class="btn btn-primary status">Aktywny</button>';
                                            } else {
                                                echo '<button type="submit" name="unsuspend" value="' . $row[0] . '" class="btn btn-warning status">Zawieszony</button>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo '<button type="submit" name="deleteUser" value="' . $row[0] . '" class="btn btn-danger delete">Usun</button>';
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
                                            echo '<li class="active" ><a href="adminPanel.php?action=allUsers&page=' . $x . '">' . $x . '</a></li>';
                                        } else {
                                            echo '<li><a href="adminPanel.php?action=allUsers&page=' . $x . '">' . $x . '</a></li>';
                                        }
                                    } else {
                                        if ($x == 1) {
                                            echo '<li class="active" ><a href="adminPanel.php?action=allUsers&page=' . $x . '">' . $x . '</a></li>';
                                        } else {
                                            echo '<li><a href="adminPanel.php?action=allUsers&page=' . $x . '">' . $x . '</a></li>';
                                        }
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                        <?php

                    }
                }
                if (!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action'] == 'allCourses')) {
                    $result = $connection->query("SELECT * FROM courses");
                    if (!$result) throw new Exception($connection->error);
                    ?>
                    <h3>Kursy:</h3>
                    <form method="post">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Nazwa</th>
                                <th class="toHideInSmall">Opis</th>
                                <th class="toHideInSmall">Cena</th>
                                <th>Kategoria</th>
                                <th>Edycja</th>
                                <th>Usun</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $rows = $result->fetch_all(MYSQLI_NUM);
                            $start = 0;
                            if (isset($_GET['page'])) {
                                $start = 10 * ($_GET['page'] - 1);
                            }
                            $koniec = 5;
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
                                    <td><?php echo $row[0] ?></td>
                                    <td><?php echo $row[1] ?></td>
                                    <td class="toHideInSmall"><?php echo $row[2] ?></td>
                                    <td class="toHideInSmall"><?php echo $row[3] ?></td>
                                    <td><?php echo $row[4] ?></td>
                                    <td>
                                        <?php
                                        echo '<button type="submit" name="editCourse" value="' . $row[0] . '" class="btn btn-succes">Edytuj</button>';
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo '<button type="submit" name="deleteCourse" value="' . $row[0] . '" class="btn btn-danger">Usun</button>';
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
                                        echo '<li class="active" ><a href="adminPanel.php?action=allCourses&page=' . $x . '">' . $x . '</a></li>';
                                    } else {
                                        echo '<li><a href="adminPanel.php?action=allCourses&page=' . $x . '">' . $x . '</a></li>';
                                    }
                                } else {
                                    if ($x == 1) {
                                        echo '<li class="active" ><a href="adminPanel.php?action=allCourses&page=' . $x . '">' . $x . '</a></li>';
                                    } else {
                                        echo '<li><a href="adminPanel.php?action=allCourses&page=' . $x . '">' . $x . '</a></li>';
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
<!--Modal login-->
<div id="id01" class="modal">
    <form class="modal-content animate" method="post" action="services/updateCourse.php">
        <div id="modalHeader">
            <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
            <h1>Aktualizacja danych o kursie</h1>
        </div>
        <div class="modalContainer">
            <div class="frame">
                <input name="id" value="<?php echo $editedCourse[0]; ?>" hidden>
                <input type="text" class="form-control" name="name" placeholder="Nazwa.."
                       value="<?php echo $editedCourse[1]; ?>">
                </br>
                <textarea class="form-control" rows="5" name="description"
                          placeholder="Opis kursu.."><?php echo $editedCourse[2]; ?></textarea>
                </br>
                <input type="text" class="form-control" name="price" placeholder="Cena kursu.."
                       value="<?php echo $editedCourse[3]; ?>">
                </br>
                <select class="form-control" name="category">
                    <option>HTML</option>
                    <option>CSS</option>
                    <option>PHP</option>
                    <option>jQuery</option>
                    <option>Bootstrap</option>
                    <option>AJAX</option>
                    <option>Django</option>
                    <option>SpringBoot</option>
                    <option>ASP .NET</option>
                    <option>DropWizard</option>
                </select>
                </br>
                <button type="submit" class="btn btn-success" style="margin-right: 10px">Zapisz</button>
                <button type="reset" onclick="document.getElementById('id01').style.display='none'"
                        class="btn btn-warning">Anuluj
                </button>
            </div>
        </div>
    </form>
</div>

<!--Skrypt do modala-->
<script>
    var modal = document.getElementById('id01');
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<?php
if (isset($_POST['editCourse'])) {
    ?>
    <script>
        document.getElementById('id01').style.display = 'block';
    </script>
    <?php
    unset($_POST['editCourse']);
}
?>
</body>
</html>
