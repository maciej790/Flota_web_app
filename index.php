<?php
require_once './dataBase/DatabaseConnection.php';
require_once './users/User.php';
$user = new User();
session_start();
if (isset($_SESSION['user'])) {
    header("Location: ./views/" . $_SESSION['user']['rola'] . "." . "php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="./css/login.css">
</head>

<body>
    <div class="login-container">
        <form method="POST" action="" class="login-form">
            <h2>Logowanie do systemu</h2>
            <div class="form-group">
                <label for="login">Login</label>
                <input type="text" id="login" name="login" placeholder="Wpisz swój login" required>
            </div>
            <div class="form-group">
                <label for="password">Hasło</label>
                <input type="password" id="password" name="password" placeholder="Wpisz swoje hasło" required>
            </div>
            <button type="submit" class="btn-submit">Zaloguj się</button>
            <p class="p-error">
                <?php
                if (isset($_POST['login']) and isset($_POST['password']))
                    $user->signIn($_POST['login'], $_POST['password']);
                ?>
            </p>
        </form>
    </div>
</body>

</html>