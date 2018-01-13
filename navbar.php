<?php
require_once 'services/Course.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_POST['deleteFromCart'])) {
    $cart = $_SESSION['cart'];
    unset($cart[$_POST['deleteFromCart']]);
    $_SESSION['cart'] = $cart;
    unset($_POST['deleteFromCart']);
}
?>
<link rel="stylesheet" type="text/css" href="style/css/navbar.css"/>
<div id="spaceForNavbar"></div>
<nav class="myNav">
    <header>
        <div class="logo">
            <img src="media/logo.png">
        </div>
        <div class="name"><a href="index.php">IT - DEV - TECH</a></div>
    </header>
    <div class="categories">
        <a href="#">KATALOG</a>
        <ul>
            <li><a href="categories.php?category=HTML">HTML</a></li>
            <li><a href="categories.php?category=CSS">CSS</a></li>
            <li><a href="categories.php?category=PHP">PHP</a></li>
            <li><a href="categories.php?category=jQuery">jQuery</a></li>
            <li><a href="categories.php?category=Bootstrap">Bootstrap</a></li>
            <li><a href="categories.php?category=AJAX">AJAX</a></li>
            <li><a href="categories.php?category=Django">Django</a></li>
            <li><a href="categories.php?category=SpringBoot">SpringBoot</a></li>
            <li><a href="categories.php?category=ASP.NET">ASP .NET</a></li>
            <li><a href="categories.php?category=DropWizard">DropWizard</a></li>
        </ul>
    </div>
    <div class="searchField">
        <form method="GET" action="categories.php">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Szukaj kursu...">
                <span class="input-group-btn">
                    <button class="btn btn-secondary" id="searchArrows" type="submit">>></button>
                </span>
            </div>
        </form>
    </div>
    <div class="buttons">
        <?php
        if (isset($_SESSION['isLogedIn']) && ($_SESSION['isLogedIn'] == true)) {
            ?>
            <?php
            if ($_SESSION['role'] == 'USER') {
                ?>
                <div class="cart">
                    <a onclick="document.getElementById('cartModal').style.display='block'">
                        <span class="glyphicon glyphicon-shopping-cart"></span><span class="cartText"> Koszyk</span></a>
                </div>
                <div class="profilDropdown">
                    <a href="#"><span class="glyphicon glyphicon-user"></span><span
                                class="profilText"> <?php echo $_SESSION['user']; ?></span></a>
                    <ul>
                        <li><a href="userPanel.php?action=myProfile">Zmiana Hasla</a></li>
                        <li><a href="userPanel.php?action=cart">Moje zakupy</a></li>
                        <li><a href="userPanel.php?action=myCourses">Moje kursy</a></li>
                        <li><a href="services/logout.php"><span class="glyphicon glyphicon-log-out"></span> Wyloguj</a>
                        </li>
                    </ul>
                </div>
                <?php
            }
            if ($_SESSION['role'] == 'ADMIN') {
                ?>
                <div class="profilDropdown">
                    <a href="adminPanel.php"><span class="glyphicon glyphicon-user"></span> Administracja</a>
                    <ul>
                        <li><a href="adminPanel.php?action=allCourses">Kursy</a></li>
                        <li><a href="adminPanel.php?action=addNewCourse">Dodaj Kurs</a></li>
                        <li><a href="adminPanel.php?action=allUsers">Uzytkownicy</a></li>
                        <li><a href="services/logout.php"><span class="glyphicon glyphicon-log-out"></span> Wyloguj</a>
                        </li>
                    </ul>
                </div>
                <?php
            }
        } else {
            ?>
            <button type="button" onclick="document.getElementById('loginModal').style.display='block'"
                    class="btn btn-primary zaloguj">Zaloguj
            </button>
            <a href="register.php">
                <button type="button" class="btn btn-primary zarejestruj">Zarejestruj</button>
            </a>
            <?php
        }
        ?>
    </div>
    <div class="navbar-header">
        <button type="button" data-toggle="collapse" data-target=".navbar-collapse" id="hamburger">
            <span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span>
        </button>
    </div>
</nav>
<div id="smallMenu">
    <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right">
            <li><a href="index.php" style="padding: 20px">
                    <img src="media/logo.png" style="width: 15px">
                    <span style="padding-top: 50px; padding-bottom: 50px">IT - DEV - TECH</span>
                </a></li>
            <span style="clear: both"></span>
            <?php
            if (isset($_SESSION['isLogedIn']) && ($_SESSION['isLogedIn'] == true)) {
                ?>
                <?php
                if ($_SESSION['role'] == 'USER') {
                    ?>
                    <li><a data-toggle="collapse" data-target=".navbar-collapse"
                           onclick="document.getElementById('cartModal').style.display='block'">
                            <span class="glyphicon glyphicon-shopping-cart"></span><span
                                    class="cartText"> Koszyk</span></a>
                    </li>
                    <li><a data-toggle="collapse" data-target=".navbar-collapse" href="userPanel.php?action=myProfile">Zmiana
                            Hasla</a></li>
                    <li><a data-toggle="collapse" data-target=".navbar-collapse"
                           href="userPanel.php?action=cart">Moje zakupy</a></li>
                    <li><a data-toggle="collapse" data-target=".navbar-collapse" href="userPanel.php?action=myCourses">Moje
                            kursy</a></li>
                    <li>
                        <a data-toggle="collapse" data-target=".navbar-collapse" href="services/logout.php">
                            <span class="glyphicon glyphicon-log-out"></span> Wyloguj</a>
                    </li>
                    <?php
                }
                if ($_SESSION['role'] == 'ADMIN') {
                    ?>
                    <li><a data-toggle="collapse" data-target=".navbar-collapse"
                           href="adminPanel.php?action=allCourses">Kursy</a></li>
                    <li><a data-toggle="collapse" data-target=".navbar-collapse"
                           href="adminPanel.php?action=addNewCourse">Dodaj Kurs</a></li>
                    <li><a data-toggle="collapse" data-target=".navbar-collapse" href="adminPanel.php?action=allUsers">Uzytkownicy</a>
                    </li>
                    <li>
                        <a data-toggle="collapse" data-target=".navbar-collapse" href="services/logout.php">
                            <span class="glyphicon glyphicon-log-out"></span> Wyloguj</a>
                    </li>
                    <?php
                }
            } else {
                ?>
                <li class="active"><a data-toggle="collapse" data-target=".navbar-collapse"
                                      onclick="document.getElementById('loginModal').style.display='block'"
                    >Zaloguj</a></li>
                <li><a data-toggle="collapse" data-target=".navbar-collapse" href="register.php">Zarejestruj</a></li>
                <?php
            }
            ?>
        </ul>
    </div>
</div>

<!--Cart login-->
<div id="cartModal" class="modal">
    <form class="modal-content animate" method="post">
        <div id="modalHeader">
                <span onclick="document.getElementById('cartModal').style.display='none'" class="close"
                      title="Close Modal">&times;</span>
            <h1>Koszyk:</h1>
        </div>
        <div class="modalContainer">
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
                            <td><?php if ($item->price == 0) {
                                    echo 'FREE';
                                } else {
                                    echo $item->price . 'zl';
                                } ?></td>
                            <td>
                                <button type="submit" value="<?php echo $item->id ?>" name="deleteFromCart"
                                        class="deleteFromCart">&times;
                                </button>
                            </td>
                        </tr>
                        <?php
                        $licznik++;
                    }
                    echo '</tbody>';
                } else {
                    ?>
                    <tbody>
                    <tr>
                        <td>
                            <div class="alert alert-info">
                                Twój koszyk jest pusty!
                            </div>
                        </td>
                    </tr>
                    </tbody>
                    <?php
                }
                ?>
            </table>
            <?php if (isset($_SESSION['cart']) && sizeof($_SESSION['cart']) > 0) { ?>
                <a href="userPanel.php?action=cart"><button type="button" id="payForCart">Przejdź do płatności</button></a>
            <?php } ?>
        </div>
    </form>
</div>
<!--Modal login-->
<div id="loginModal" class="modal">
    <form class="modal-content animate" action="services/login.php" method="post">
        <div class="modalImage">
            <span onclick="document.getElementById('loginModal').style.display='none'" class="close"
                  title="Close Modal">&times;</span>
            <img src="media/logo.png" alt="Avatar" class="avatar">
        </div>
        <div id="modalHeader">
            <h1>Zaloguj sie!</h1>
        </div>
        <div class="modalContainer">
            <input type="text" class="loginInput" placeholder="Login" name="login" required="required"/>
            <input type="password" class="loginInput" placeholder="Haslo" name="password" required="required"/>
            <?php
            if (isset($_SESSION['login_error'])) {
                ?>
                <script>
                    document.getElementById('loginModal').style.display = 'block';
                </script>
                <div class="alert alert-danger" style="width: 60%; margin-left: 20%">
                    <?php echo $_SESSION['login_error']; ?>
                </div>
                <?php
                unset($_SESSION['login_error']);
            }
            ?>
            <button type="submit" id="modalLoginButton">Zaloguj</button>
            <br/>
            <span class="modalForgotPassword"><a href="#">Zapomniałeś hasła?</a></span>
            <div style="clear: both"><br/></div>
        </div>
    </form>
</div>


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
