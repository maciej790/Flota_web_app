<?php
require_once "../users/Admin.php";
$admin = new Admin();
session_start();
if (!isset($_SESSION['user']) or $_SESSION['user']['rola'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/admin-dashboard.css">
</head>

<body>
    <div class="dashboard">
        <!-- Lewa kolumna -->
        <aside class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="?add">Dodaj kierownika floty</a></li>
                <li><a href="admin.php">Lista kierowników floty</a></li>
            </ul>
        </aside>

        <!-- Górny pasek -->
        <header class="topbar">
            <h1>Panel Administratora</h1>
            <form action="" method="get" class="topbar__form">
                <h3><?php echo $_SESSION['user']['imie'] . ' ' . $_SESSION['user']['nazwisko'] ?></h3>
                <input type="submit" name="logout" value="Wyloguj się" class="logout" />
            </form>
        </header>

        <!-- Obszar roboczy -->
        <main class="content">
            <?php
            if (isset($_GET['add'])) {
                addManagerForm($admin);
            } else if (isset($_GET['edit'])) {
                editManagerForm($admin);
            } else if (isset($_GET['delete'])) {
                deleteManagerForm($admin);
            } else {
                mainDashboard($admin);
            }

            function addManagerForm($admin)
            {
            ?>
                <div class="styled__form">
                    <h2>Dodaj nowego Kierownika floty</h2>
                    <?php if (isset($message)) echo "<p>$message</p>"; ?>
                    <form method="POST" action="">
                        <label for="imie">Imię:</label>
                        <input type="text" id="imie" name="imie" required>

                        <label for="nazwisko">Nazwisko:</label>
                        <input type="text" id="nazwisko" name="nazwisko" required>

                        <label for="pesel">PESEL:</label>
                        <input type="number" id="pesel" name="pesel" required>

                        <label for="mail">Mail:</label>
                        <input type="email" id="mail" name="mail" required>

                        <label for="login">Login:</label>
                        <input type="text" id="login" name="login" required>

                        <label for="password">Hasło:</label>
                        <input type="password" id="password" name="password" required>

                        <button type="submit" name="add">Dodaj</button>
                    </form>
                    <?php
                    $imie = $_POST['imie'] ?? '';
                    $nazwisko = $_POST['nazwisko'] ?? '';
                    $pesel = $_POST['pesel'] ?? '';
                    $mail = $_POST['mail'] ?? '';
                    $login = $_POST['login'] ?? '';
                    $password = $_POST['password'] ?? '';

                    if (isset($_POST['add'])) {
                        if ($imie && $nazwisko && $pesel && $mail && $login && $password) {
                            $result = $admin->addManager($imie, $nazwisko, $pesel, $mail, $login, $password);
                            if ($result) {
                                echo  "<h3 class='komunikat'>Kierownik został dodany pomyślnie.</h3>";
                            } else {
                                echo  "<h3 class='error'>Wystąpił błąd podczas dodawania kierownika. </h3>";
                            }
                        }
                    }

                    ?>
                </div>



            <?php
            }
            function editManagerForm($admin)
            {
            ?>
                <?php
                $manager = $admin->getManagerById($_GET['edit']);
                ?>
                <?php if (isset($message)) echo "<p>$message</p>"; ?>
                <?php
                echo '<div class="styled__form">';
                echo '<h2>Edytuj dane Kierownika floty</h2>';
                echo '<form action="" method="post">';
                echo '<input type="hidden" name="id_osoby" value="' . htmlspecialchars($manager['id_osoby']) . '">';

                echo '<label>Imię:</label>';
                echo '<input required type="text" name="imie" value="' . htmlspecialchars($manager['imie']) . '">';

                echo '<label>Nazwisko:</label>';
                echo '<input required type="text" name="nazwisko" value="' . htmlspecialchars($manager['nazwisko']) . '">';

                echo '<label>PESEL:</label>';
                echo '<input required type="number" name="pesel" value="' . htmlspecialchars($manager['pesel']) . '">';

                echo '<label>Mail:</label>';
                echo '<input required type="email" name="mail" value="' . htmlspecialchars($manager['mail']) . '">';

                echo '<label>Login:</label>';
                echo '<input required type="text" name="login" value="' . htmlspecialchars($manager['login']) . '">';

                echo '<label>Hasło:</label>';
                echo '<input required type="password" name="password" value="' . htmlspecialchars($manager['password']) . '">';

                echo '<button type="submit" name="edit_manager">Zapisz zmiany</button>';
                echo '</form>';
                if (isset($_POST['edit_manager'])) {
                    $imie = isset($_POST['imie']) ? $_POST['imie'] : null;
                    $nazwisko = isset($_POST['nazwisko']) ? $_POST['nazwisko'] : null;
                    $mail = isset($_POST['mail']) ? $_POST['mail'] : null;
                    $pesel = isset($_POST['pesel']) ? $_POST['pesel'] : null;
                    $login = isset($_POST['login']) ? $_POST['login'] : null;
                    $password = isset($_POST['password']) ? $_POST['password'] : null;

                    // Wywołanie metody edytowania menedżera
                    $result = $admin->editManager($_GET['edit'], $imie, $nazwisko, $mail, $pesel, $login, $password);
                    if ($result) {
                        echo "<h3 class='komunikat'>Dane kierownika zostały zaaktualizowane!</h3>";
                    } else {
                        echo "<h3 class='error'>Dane zostały błędnie wprowadzone, proszę je poprawić!</h3>";
                    }
                }
                echo '</div>';

                ?>

                <?php


                ?>

            <?php

            }
            function deleteManagerForm($admin)
            {
            ?>
                <div class="delete__form">
                    <form method="POST" action="">
                        <label for="delete">
                            Czy na pewno chcesz trwale usunąć kierownika floty: <br>
                            <?php
                            $manager = $admin->getManagerById($_GET['delete']);
                            echo $manager['imie'] . ' ' . $manager['nazwisko'];
                            ?>
                            <br>
                            Tej czynności nie można cofnąć
                            <br>
                        </label>
                        <input type="submit" id="delete-true" name="delete-true" value="tak">
                        <input type="submit" id="delete-false" name="delete-false" value="nie">
                    </form>
                </div>

                <?php
                if (isset($_POST['delete-true'])) {
                    $admin->deleteManager($_GET['delete']);
                    header('Location:admin.php');
                } else if (isset($_POST['delete-false'])) {
                    header('Location:admin.php');
                }


                ?>


            <?php
            }

            function mainDashboard($admin)
            {

                $managers = $admin->getAllManagers();

                if (isset($_GET['logout'])) {
                    unset($_SESSION['user']);
                    header("Location: ../index.php");
                }

                echo '<table border="1" cellpadding="10" cellspacing="0">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Imię</th>';
                echo '<th>Nazwisko</th>';
                echo '<th>Login</th>';
                echo '<th>Mail</th>';
                echo '<th>Pesel</th>';
                echo '<th>Akcje</th>'; // Kolumna dla opcji
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';


                if (!empty($managers)) {
                    foreach ($managers as $manager) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($manager['imie']) . '</td>';
                        echo '<td>' . htmlspecialchars($manager['nazwisko']) . '</td>';
                        echo '<td>' . htmlspecialchars($manager['login']) . '</td>';
                        echo '<td>' . htmlspecialchars($manager['mail']) . '</td>';
                        echo '<td>' . htmlspecialchars($manager['pesel']) . '</td>';
                        echo '<td>';
                        echo '<a href="?edit=' . htmlspecialchars($manager['id_osoby']) . '" class="edit">Edytuj</a> | ';
                        echo '<a href="?delete=' . htmlspecialchars($manager['id_osoby']) . '" class="delete">Usuń</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6">Brak kierowników do wyświetlenia</td></tr>';
                }

                echo '</tbody>';
                echo '</table>';
            }

            ?>
        </main>
    </div>

</body>

</html>






</html>