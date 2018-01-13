<?php
session_start();
if (isset($_POST['email'])) {
    $is_validated = true;

    //login
    $login = $_POST['login'];
    //login length
    if ((strlen($login) < 3) || (strlen($login) > 20)) {
        $is_validated = false;
        $_SESSION['e_login'] = "login musi posiadac od 3 do 20 znakow";
    }
    //login alphanumeric
    if (!ctype_alnum($login)) {
        $is_validated = false;
        $_SESSION['e_login'] = "Login może skladac sie tylko z liter i cyfr (bez polskich znakow)";
    }

    //email
    $email = $_POST['email'];
    $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
    if ((filter_var($emailB, FILTER_VALIDATE_EMAIL) == false) || ($emailB != $email)) {
        $is_validated = false;
        $_SESSION['e_email'] = "Podaj poprawny adres email";
    }

    //password
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

    //checkbox
    if (!isset($_POST['regulamin'])) {
        $is_validated = false;
        $_SESSION['e_regulamin'] = "W celu rejestracji potwierdz regulamin";
    }

    //recaptha
//    kalwador.pl
//    $secret_captha_key = "6LcAwT8UAAAAAKQNXHEdqjsfCzVzNslDPC6U105K";
//    localhost
    $secret_captha_key = "6LdCvTsUAAAAAOaXK_YmlA7N6pAfBfheglWhqwHG";
    $check_captha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_captha_key . '&response=' . $_POST['g-recaptcha-response']);
    $response_captha = json_decode($check_captha);
    if ($response_captha->success == false) {
        $is_validated = false;
        $_SESSION['e_captha'] = "Potwierdz ze nie jestes botem";
    }

    //Save inputed data
    $_SESSION['fr_login'] = $login;
    $_SESSION['fr_email'] = $email;
    $_SESSION['fr_password1'] = $password1;
    $_SESSION['fr_password2'] = $password2;
    if (isset($_POST['regulamin'])) $_SESSION['fr_regulamin'] = true;

    require_once "services/dbConfig.php";
    mysqli_report(MYSQLI_REPORT_STRICT);
    try {
        $connection = new mysqli($host, $db_user, $db_password, $db_name);
        if ($connection->connect_errno != 0) {
            throw new Exception(mysqli_connect_errno());
        } else {
            //is Email in db
            $result = $connection->query("SELECT id FROM users WHERE email = '$email'");
            if (!$result) throw new Exception($connection->error);

            if ($result->num_rows > 0) {
                $is_validated = false;
                $_SESSION['e_email'] = "Istnieje juz konto przypisane do tego adresu email!";
            }

            $result = $connection->query("SELECT id FROM users WHERE username = '$login'");
            if (!$result) throw new Exception($connection->error);

            if ($result->num_rows > 0) {
                $is_validated = false;
                $_SESSION['e_login'] = "Istnieje juz konto o takim loginie!";
            }

            $activation_link = bin2hex(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));

            if ($is_validated) {
                if ($connection->query("INSERT INTO users VALUES (NULL, '$login','$password_hash','$email','USER',0,'$activation_link')")) {

                    //WYSYlanie maila
                    $subject = 'IT-DEV-TECH - Aktywacja konta';
                    $message = '
                    <html>
                    <head>
                      <title>Witaj ' . $login . ',</title>
                    </head>
                    <body>
                      <p>Wlasnie zarejestrowales sie w serwisie IT-DEV-TECH.<br/> 
                      Aby aktywowac swoje konto nacisnij ponizszy link lub przeklej go do swojej przeglądarki:</p>
                      </br>
                      <p>www.kalwador.pl/kursy/activation.php?key=' . $activation_link . '</p>
                      </br>
                      <p>Pozdrawiamy,</p>
                      <p>Zespol IT-DEV-TECH</p>
                    </body>
                    </html>
                    ';

                    $headers = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-2' . "\r\n";
                    mail($email, $subject, $message, $headers);


                    $_SESSION['succesful_register'] = true;
                    header('Location: successfulRegister.php');
                } else {
                    throw new Exception($connection->error);
                }
            }
            $connection->close();
        }
    } catch (Exception $e) {
        echo '<span style="color:red;">Blad serwera. Prosimy o rejestracje w innym terminie!</span>';
        echo '<br/>Informacja developerska:' . $e;
    }
}
?>
<!DOCTYPE HTML>
<html label="pl">
<head>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="style/css/register.css">
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
<div class="myContainer">
    <form method="post" id="signup">
        <div class="header">
            <h3>Rejestracja</h3>
            <p>Zarejestruj sie aby uzyskać dostep do kursow</p>
        </div>
        <div class="sep"></div>
        <div class="inputs">
            <input type="text" name="login" placeholder="Login" value="<?php
            if (isset($_SESSION['fr_login'])) {
                echo $_SESSION['fr_login'];
                unset($_SESSION['fr_login']);
            }
            ?>" autofocus/>
            <?php
            if (isset($_SESSION['e_login'])) {
                echo '<div class="error">' . $_SESSION['e_login'] . '</div>';
                unset($_SESSION['e_login']);
            }
            ?>
            <input type="text" name="email" placeholder="e-mail" value="<?php
            if (isset($_SESSION['fr_email'])) {
                echo $_SESSION['fr_email'];
                unset($_SESSION['fr_email']);
            }
            ?>"/>
            <?php
            if (isset($_SESSION['e_email'])) {
                echo '<div class="error">' . $_SESSION['e_email'] . '</div>';
                unset($_SESSION['e_email']);
            }
            ?>
            <input type="password" name="password1" placeholder="Haslo" value="<?php
            if (isset($_SESSION['fr_password1'])) {
                echo $_SESSION['fr_password1'];
                unset($_SESSION['fr_password1']);
            }
            ?>"/>
            <?php
            if (isset($_SESSION['e_password'])) {
                echo '<div class="error">' . $_SESSION['e_password'] . '</div>';
                unset($_SESSION['e_password']);
            }
            ?>
            <input type="password" name="password2" placeholder="Powtorz Haslo" value="<?php
            if (isset($_SESSION['fr_password2'])) {
                echo $_SESSION['fr_password2'];
                unset($_SESSION['fr_password2']);
            }
            ?>"/>
            <br/>
            <label>
                <input type="checkbox" name="regulamin" <?php
                if (isset($_SESSION['fr_regulamin'])) {
                    echo "checked";
                    unset($_SESSION['fr_regulamin']);
                }
                ?>/>Akceptuje Regulamin</label>
            <br/>
            <?php
            if (isset($_SESSION['e_regulamin'])) {
                echo '<div class="error">' . $_SESSION['e_regulamin'] . '</div>';
                unset($_SESSION['e_regulamin']);
            }
            ?>
            <!--kalwador.pl-->
<!--            <div class="g-recaptcha" data-sitekey="6LcAwT8UAAAAAEpQpds9lU-_TAsk46h5j8n9yes5"></div>-->
            <!--localhost-->
                        <div class="g-recaptcha" data-sitekey="6LdCvTsUAAAAAFJLYZyfeGZ9fsGnn28uhrOZhqeX"></div>
            <?php
            if (isset($_SESSION['e_captha'])) {
                echo '<div class="error">' . $_SESSION['e_captha'] . '</div>';
                unset($_SESSION['e_captha']);
            }
            ?>
            <input type="submit" id="submit">
        </div>
        <br/>
        <div style="text-align: center">
            <!--            do poprawienia aby nie dzialo na calym oknie!!!-->
            <!--            <a href="../index.php">Powrot do strony glownej</a>-->
        </div>
    </form>
</div>
</body>
</html>