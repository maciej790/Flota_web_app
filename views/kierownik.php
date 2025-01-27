<?php
require_once "../users/Manager.php";
$manager = new Manager();
session_start();
if (!isset($_SESSION['user']) or $_SESSION['user']['rola'] !== 'kierownik') {
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
                <li><a href="?add_employer">Dodaj pracownika</a></li>
                <li><a href="kierownik.php">Lista pracowników</a></li>
                <li><a href="?add_vehicle">Dodaj pojazd</a></li>
                <li><a href="?pojazdy">Lista pojazdów</a></li>
                <li><a href="?zapytania">Zapytania oczekujące</a></li>
                <li><a href="?przydzialy">Lista przydzielonych pojazdów</a></li>
                <li><a href="?report">Raport</a></li>
            </ul>
        </aside>

        <!-- Górny pasek -->

        <header class="topbar">
            <h1>Panel Kierownika floty</h1>
            <form action="" method="get" class="topbar__form">
                <h3><?php echo $_SESSION['user']['imie'] . ' ' . $_SESSION['user']['nazwisko'] ?></h3>
                <input type="submit" name="logout" value="Wyloguj się" class="logout" />
            </form>
        </header>

        <!-- Obszar roboczy -->
        <main class="content">
            <?php
            if (isset($_GET['add_employer'])) {
                addEmployer($manager);
            } else if (isset($_GET['edit'])) {
                editEmployerForm($manager);
            } else if (isset($_GET['delete'])) {
                deleteEmployerForm($manager);
            } else if (isset($_GET['przydzialy'])) {
                assignmentsDashboard($manager);
            } else if (isset($_GET['zapytania'])) {
                requests($manager);
            } else if (isset($_GET['accept'])) {
                acceptRequestForm($manager);
            } else if (isset($_GET['decline'])) {
                declineRequest($manager);
            } else if (isset($_GET['delete_assigment'])) {
                deleteAsigment($manager);
            } else if (isset($_GET['pojazdy'])) {
                vehiclesDashboard($manager);
            } else if (isset($_GET['edit_vehicle'])) {
                vehicleEdit($manager);
            } else if (isset($_GET['remove_vehicle'])) {
                removeVehicle($manager);
            } else if (isset($_GET['add_vehicle'])) {
                addVehicle($manager);
            } else if (isset($_GET['report'])) {
                reportDashboard($manager);
            } else {
                mainDashboard($manager);
            }

            function addEmployer($manager)
            {
            ?>
                <div class="styled__form">
                    <h2>Dodaj Pracownika</h2>
                    <?php if (isset($message)) echo "<p>$message</p>"; ?>
                    <form method="POST" action="">
                        <label for="imie">Imię:</label>
                        <input type="text" required id="imie" name="imie" required>

                        <label for="nazwisko">Nazwisko:</label>
                        <input required type="text" id="nazwisko" name="nazwisko" required>

                        <label for="pesel">PESEL:</label>
                        <input required type="number" id="pesel" name="pesel" required>

                        <label for="mail">Mail:</label>
                        <input required type="email" id="mail" name="mail" required>

                        <label for="rola">Rola:</label>
                        <select required name="rola" id="mail" required>
                            <option value="pracownik">pracownik</option>
                            <option value="serwisant">serwisant</option>
                        </select>

                        <label for="login">Login:</label>
                        <input required type="text" id="login" name="login" required>

                        <label for="password">Hasło:</label>
                        <input required type="password" id="password" name="password" required>

                        <button type="submit" name="add">Dodaj</button>
                    </form>
                    <?php
                    $imie = $_POST['imie'] ?? '';
                    $nazwisko = $_POST['nazwisko'] ?? '';
                    $pesel = $_POST['pesel'] ?? '';
                    $mail = $_POST['mail'] ?? '';
                    $login = $_POST['login'] ?? '';
                    $rola = $_POST['rola'] ?? '';
                    $password = $_POST['password'] ?? '';

                    if (isset($_POST['add'])) {
                        if ($imie && $nazwisko && $pesel && $mail && $login && $rola && $password) {
                            $result = $manager->addEmployer($imie, $nazwisko, $pesel, $mail, $login, $rola, $password);
                            if ($result) {
                                echo  "<h3 class='komunikat'>Pracownik został dodany pomyślnie.</h3>";
                            } else {
                                echo  "<h3 class='error'>Wystąpił błąd podczas dodawania Pracownika.</h3>";
                            }
                        }
                    }

                    ?>
                </div>




            <?php
            }
            function editEmployerForm($manager)
            {
            ?>
                <?php
                $employer = $manager->getEmployerById($_GET['edit']);
                ?>
                <?php if (isset($message)) echo "<p>$message</p>"; ?>
                <?php
                echo '<div class="styled__form">';
                echo '<h2>Edytuj dane pracownika</h2>';
                echo '<form action="" method="post">';
                echo '<input type="hidden" name="id_osoby" value="' . htmlspecialchars($employer['id_osoby']) . '" required>';

                echo '<label>Imię:</label>';
                echo '<input type="text" name="imie" value="' . htmlspecialchars($employer['imie']) . '" required><br>';

                echo '<label>Nazwisko:</label>';
                echo '<input type="text" name="nazwisko" value="' . htmlspecialchars($employer['nazwisko']) . '" required><br>';

                echo '<label>PESEL:</label>';
                echo '<input type="number" name="pesel" value="' . htmlspecialchars($employer['pesel']) . '" required><br>';

                echo '<label>Mail:</label>';
                echo '<input type="email" name="mail" value="' . htmlspecialchars($employer['mail']) . '" required><br>';

                echo '<label for="rola">Rola:</label>';
                echo '<select name="rola" id="rola" required>';
                echo '<option value="pracownik"' . ($employer['rola'] === 'pracownik' ? ' selected' : '') . '>pracownik</option>';
                echo '<option value="serwisant"' . ($employer['rola'] === 'serwisant' ? ' selected' : '') . '>serwisant</option>';
                echo '</select><br>';

                echo '<label>Login:</label>';
                echo '<input type="text" name="login" value="' . htmlspecialchars($employer['login']) . '" required><br>';

                echo '<label>Hasło:</label>';
                echo '<input type="password" name="password" value="' . htmlspecialchars($employer['password']) . '" required><br>';

                echo '<button type="submit" name="edit_manager">Zapisz zmiany</button>';
                echo '</form>';

                if (isset($_POST['edit_manager'])) {
                    $imie = isset($_POST['imie']) ? $_POST['imie'] : null;
                    $nazwisko = isset($_POST['nazwisko']) ? $_POST['nazwisko'] : null;
                    $mail = isset($_POST['mail']) ? $_POST['mail'] : null;
                    $pesel = isset($_POST['pesel']) ? $_POST['pesel'] : null;
                    $login = isset($_POST['login']) ? $_POST['login'] : null;
                    $rola = isset($_POST['rola']) ? $_POST['rola'] : null;
                    $password = isset($_POST['password']) ? $_POST['password'] : null;

                    // Wywołanie metody edytowania menedżera
                    $result = $manager->editEmployer($_GET['edit'], $imie, $nazwisko, $mail, $pesel, $login, $rola, $password);
                    if ($result) {
                        echo "<h3 class='komunikat'>Dane Pracownika zostały zaaktualizowane!</h3>";
                    } else {
                        echo "<h3 class='error'>Dane zostały błędnie wprowadzone, proszę je poprawić!</h3>";
                    }
                }


                echo '</div>';
                ?>



            <?php

            }
            function deleteEmployerForm($manager)
            {
            ?>
                <div class="delete__form">
                    <form method="POST" action="">
                        <label for="delete">
                            Czy na pewno chcesz trwale usunąć Pracownika: <br>
                            <?php
                            $employer = $manager->getEmployerById($_GET['delete']);
                            echo $employer['imie'] . ' ' . $employer['nazwisko'];
                            ?>
                            <br>
                            Tej czynności nie można cofnąć
                            <br>
                        </label>
                        <input type="submit" id="delete-true" name="delete-true" value="tak">
                        <input type="submit" id="delete-false" name="delete-false" value="nie">
                    </form>
                    <?php

                    if (isset($_POST['delete-true'])) {
                        try {
                            $manager->deleteEmployer($_GET['delete']);
                            header('Location:admin.php');
                        } catch (Exception $e) {
                            echo '<br>';
                            echo "<div class='error-message'>Ten użytkownik ma utworzony aktywny przydział na pojazd! Najpierw zakończ przydział. </div>";
                        }
                    } else if (isset($_POST['delete-false'])) {
                        header('Location:admin.php');
                    }


                    ?>


                </div>

            <?php
            }

            function mainDashboard($manager)
            {

                $employers = $manager->getAllEmployers();

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
                echo '<th>Rola</th>';
                echo '<th>Pesel</th>';
                echo '<th>Akcje</th>'; // Kolumna dla opcji
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';


                if (!empty($employers)) {
                    foreach ($employers as $manager) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($manager['imie']) . '</td>';
                        echo '<td>' . htmlspecialchars($manager['nazwisko']) . '</td>';
                        echo '<td>' . htmlspecialchars($manager['login']) . '</td>';
                        echo '<td>' . htmlspecialchars($manager['mail']) . '</td>';
                        echo '<td>' . htmlspecialchars($manager['rola']) . '</td>';
                        echo '<td>' . htmlspecialchars($manager['pesel']) . '</td>';
                        echo '<td>';
                        echo '<a href="?edit=' . htmlspecialchars($manager['id_osoby']) . '" class="edit">Edytuj</a> | ';
                        echo '<a href="?delete=' . htmlspecialchars($manager['id_osoby']) . '" class="delete">Usuń</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6">Brak Pracowników do wyświetlenia</td></tr>';
                }

                echo '</tbody>';
                echo '</table>';
            }

            function assignmentsDashboard($manager)
            {
                $assigments = $manager->getAssigments();

                if (isset($_GET['logout'])) {
                    unset($_SESSION['user']);
                    header("Location: ../index.php");
                }

                echo '<table border="1" cellpadding="10" cellspacing="0">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Imię</th>';
                echo '<th>Nazwisko</th>';
                echo '<th>Marka pojazdu</th>';
                echo '<th>Model pojazdu</th>';
                echo '<th>Data początku</th>';
                echo '<th>Data końca</th>';
                echo '<th>Akcje</th>'; // Kolumna dla opcji
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';


                if (!empty($assigments)) {
                    foreach ($assigments as $assigment) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($assigment['imie']) . '</td>';
                        echo '<td>' . htmlspecialchars($assigment['nazwisko']) . '</td>';
                        echo '<td>' . htmlspecialchars($assigment['marka']) . '</td>';
                        echo '<td>' . htmlspecialchars($assigment['model']) . '</td>';
                        echo '<td>' . htmlspecialchars($assigment['data_poczatek']) . '</td>';
                        echo '<td>' . htmlspecialchars($assigment['data_koniec']) . '</td>';
                        echo '<td>';
                        echo '<a href="?delete_assigment=' . htmlspecialchars($assigment['id_wypozyczenia']) . '" class="delete">Anuluj przydział</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6">Brak utworzonych przydziałów</td></tr>';
                }

                echo '</tbody>';
                echo '</table>';
            }

            function requests($manager)
            {
                $requests = $manager->getRequests();

                if (isset($_GET['logout'])) {
                    unset($_SESSION['user']);
                    header("Location: ../index.php");
                }

                echo '<table border="1" cellpadding="10" cellspacing="0">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Imię</th>';
                echo '<th>Nazwisko</th>';
                echo '<th>Marka pojazdu</th>';
                echo '<th>Model pojazdu</th>';
                echo '<th>Data początku</th>';
                echo '<th>Data końca</th>';
                echo '<th>Uzasadnienie</th>';
                echo '<th>Akcje</th>'; // Kolumna dla opcji
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';


                if (!empty($requests)) {
                    foreach ($requests as $requests) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($requests['imie']) . '</td>';
                        echo '<td>' . htmlspecialchars($requests['nazwisko']) . '</td>';
                        echo '<td>' . htmlspecialchars($requests['marka']) . '</td>';
                        echo '<td>' . htmlspecialchars($requests['model']) . '</td>';
                        echo '<td>' . htmlspecialchars($requests['data_poczatek']) . '</td>';
                        echo '<td>' . htmlspecialchars($requests['data_koniec']) . '</td>';
                        echo '<td>' . htmlspecialchars($requests['uzasadnienie']) . '</td>';
                        echo '<td>';
                        echo '<a href="?accept=' . htmlspecialchars($requests['id_zapytania']) . '" class="edit">Zatwierdz przydział</a>';
                        echo '<a href="?decline=' . htmlspecialchars($requests['id_zapytania']) . '" class="delete">Odrzuć przydział</a>';

                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6">Brak zapytań do wyświetlenia</td></tr>';
                }

                echo '</tbody>';
                echo '</table>';
            }

            function acceptRequestForm($manager)
            {
                $manager->accpetRequest($_GET['accept']);
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }

            function  declineRequest($manager)
            {
                $manager->declineRequest($_GET['decline']);
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }

            function deleteAsigment($manager)
            {

            ?>
                <div class="delete__form">
                    <form method="POST" action="">
                        <label for="delete">
                            Czy na pewno chcesz trwale usunąć przydział pojazdu: <br>
                            Tej czynności nie można cofnąć
                            <br>
                        </label>
                        <input type="submit" id="delete-true" name="delete-true" value="tak">
                        <input type="submit" id="delete-false" name="delete-false" value="nie">
                    </form>
                    <?php
                    if (isset($_POST['delete-true'])) {
                        $manager->deleteAsigment($_GET['delete_assigment']);
                        header('Location: kierownik.php?przydzialy ');
                    } else if (isset($_POST['delete-false'])) {
                        header('Location: kierownik.php?przydzialy ');
                    }


                    ?>

                </div>
            <?php
            }

            function vehiclesDashboard($manager)
            {

                $vehicles = $manager->getAllVehicles();

                if (isset($_GET['logout'])) {
                    unset($_SESSION['user']);
                    header("Location: ../index.php");
                }

                echo '<table border="1" cellpadding="10" cellspacing="0">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Marka</th>';
                echo '<th>Model</th>';
                echo '<th>Uwagi od serwisanta</th>';
                echo '<th>Rok produkcji</th>';
                echo '<th>Status</th>';
                echo '<th>Data przeglądu</th>'; // Kolumna dla opcji
                echo '<th>Akcje</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';


                if (!empty($vehicles)) {
                    foreach ($vehicles as $vehicle) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($vehicle['marka']) . '</td>';
                        echo '<td>' . htmlspecialchars($vehicle['model']) . '</td>';
                        echo '<td>' . htmlspecialchars($vehicle['dane_serwisowe']) . '</td>';
                        echo '<td>' . htmlspecialchars($vehicle['rok_produkcji']) . '</td>';
                        echo '<td>' . htmlspecialchars($vehicle['status']) . '</td>';
                        echo '<td>' . htmlspecialchars($vehicle['data_przegladu']) . '</td>';
                        echo '<td>';
                        echo '<a href="?edit_vehicle=' . htmlspecialchars($vehicle['id_pojazdu']) . '" class="edit">Edytuj</a> | ';
                        echo '<a href="?remove_vehicle=' . htmlspecialchars($vehicle['id_pojazdu']) . '" class="delete">Usuń</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6">Brak pojazdów do wyświetlenia</td></tr>';
                }

                echo '</tbody>';
                echo '</table>';
            }



            function vehicleEdit($manager)
            {
            ?>
                <?php
                $vehicle = $manager->getVehicelById($_GET['edit_vehicle']);
                ?>
                <?php if (isset($message)) echo "<p>$message</p>"; ?>
                <?php
                echo '<div class="styled__form">';
                echo '<h2>Edytuj dane pojazdu</h2>';
                echo '<form action="" method="post">';

                echo '<label>Marka:</label>';
                echo '<input type="text" name="marka" required value="' . htmlspecialchars($vehicle['marka']) . '"><br>';

                echo '<label>Model:</label>';
                echo '<input type="text" required name="model" value="' . htmlspecialchars($vehicle['model']) . '"><br>';

                echo '<label>Dane serwisowe:</label>';
                echo '<input type="text" required name="dane_serwisowe" value="' . htmlspecialchars($vehicle['dane_serwisowe']) . '"><br>';

                echo '<label>Rok produkcji:</label>';
                echo '<input type="date" required name="naped" value="' . htmlspecialchars($vehicle['rok_produkcji']) . '"><br>';

                echo '<label for="status">Status:</label>';
                echo '<select name="status" id="status" required>';
                echo '<option value="dostepny"' . ($vehicle['status'] === 'dostepny' ? ' selected' : '') . '>Dostępny</option>';
                echo '<option value="niedostepny"' . ($vehicle['status'] === 'niedostepny' ? ' selected' : '') . '>Niedostępny</option>';
                echo '<option value="serwisowany"' . ($vehicle['status'] === 'serwisowany' ? ' selected' : '') . '>Serwisowany</option>';
                echo '</select><br>';

                echo '<label>Data przeglądu:</label>';
                echo '<input required type="date" name="data_przegladu" value="' . htmlspecialchars($vehicle['data_przegladu']) . '"><br>';

                echo '<label>Przebieg:</label>';
                echo '<input required type="number" name="przebieg" value="' . htmlspecialchars($vehicle['przebieg']) . '"><br>';

                echo '<button type="submit" name="edit_manager">Zapisz zmiany</button>';
                echo '</form>';

                if (isset($_POST['edit_manager'])) {
                    $marka = isset($_POST['marka']) ? $_POST['marka'] : null;
                    $model = isset($_POST['model']) ? $_POST['model'] : null;
                    $dane_serwisowe = isset($_POST['dane_serwisowe']) ? $_POST['dane_serwisowe'] : null;
                    $status = isset($_POST['status']) ? $_POST['status'] : null;
                    $data_przegladu = isset($_POST['data_przegladu']) ? $_POST['data_przegladu'] : null;
                    $przebieg = isset($_POST['przebieg']) ? $_POST['przebieg'] : null;

                    // Wywołanie metody edytowania menedżera
                    $result = $manager->editVehicle($_GET['edit_vehicle'], $marka, $model, $dane_serwisowe, $status, $data_przegladu, $przebieg);
                    if ($result) {
                        echo " <h3 class='komunikat'>Dane pojazdu zostały zaaktualizowane!</h3>";
                    } else {
                        echo "<h3 class='error'>Dane zostały błędnie wprowadzone, proszę je poprawić!</h3>";
                    }
                }


                echo '</div>';
                ?>




            <?php

            }


            function removeVehicle($manager)
            {
            ?>
                <div class="delete__form">
                    <form method="POST" action="">
                        <label for="delete">
                            Czy na pewno chcesz trwale usunąć pojazd z floty: <br>
                            Tej czynności nie można cofnąć
                            <br>
                        </label>
                        <input type="submit" id="delete-true" name="delete-true" value="tak">
                        <input type="submit" id="delete-false" name="delete-false" value="nie">
                    </form>
                    <?php
                    if (isset($_POST['delete-true'])) {
                        try {
                            $manager->deleteVehilce($_GET['remove_vehicle']);
                            header('Location: kierownik.php?pojazdy ');
                        } catch (Exception $e) {
                            // Wyświetl tylko Twój komunikat
                            echo '<br>';
                            echo "<div class='error-message'>Pojazd jest w użytkowaniu! Najpierw zakończ przydział na ten pojazd lub odrzuć aktywne zapytanie.</div>";
                        }
                    } else if (isset($_POST['delete-false'])) {
                        header('Location: kierownik.php?pojazdy ');
                    }


                    ?>

                </div>
            <?php
            }

            function addVehicle($manager)
            {
            ?>
                <div class="styled__form">
                    <h2>Dodaj Pojazd do floty</h2>
                    <?php if (isset($message)) echo "<p>$message</p>"; ?>
                    <form method="POST" action="">
                        <label for="marka">Marka:</label>
                        <input required type="text" id="imie" name="marka" required>

                        <label for="model">Model:</label>
                        <input required type="text" id="nazwisko" name="model" required>

                        <label for="dane_serwisowe">Dane serwisowe:</label>
                        <input required type="text" id="pesel" name="dane_serwisowe" value="brak danych">

                        <label for="rok_produkcji">Rok produkcji:</label>
                        <input required type="date" id="mail" name="rok_produkcji" required>

                        <label for="status">Status:</label>
                        <select name="status" id="mail" value="dostepny" required>
                            <option value="niedostepny">Niedostępny</option>
                            <option value="serwisowany">Serwisowany</option>
                            <option value="dostepny">Dostępny</option>
                        </select>

                        <label for="data_przegladu">Data przeglądu:</label>
                        <input required type="date" id="login" name="data_przegladu" required>

                        <label for="przebieg">Przebieg:</label>
                        <input required type="number" id="password" name="przebieg" required>

                        <button type="submit" name="add">Dodaj</button>
                    </form>

                    <?php
                    $marka = $_POST['marka'] ?? '';
                    $model = $_POST['model'] ?? '';
                    $dane_serwisowe = $_POST['dane_serwisowe'] ?? '';
                    $rok_produkcji = $_POST['rok_produkcji'] ?? '';
                    $status = $_POST['status'] ?? '';
                    $data_przegladu = $_POST['data_przegladu'] ?? '';

                    if (isset($_POST['add'])) {
                        if ($marka && $model && $dane_serwisowe && $rok_produkcji && $status && $data_przegladu) {
                            $result = $manager->addVehicle($marka, $model, $dane_serwisowe, $rok_produkcji, $status, $data_przegladu);
                            if ($result) {
                                echo  "<h3 class='komunikat'>Pojazd został dodany pomyślnie.</h3>";
                            } else {
                                echo  "<h3 class='error'>Wystąpił błąd podczas dodawania pojazdu.</h3>";
                            }
                        }
                    }

                    ?>
                </div>




            <?php

            }
            function reportDashboard($manager)
            {
                $report = $manager->generateReport();

                // Sprawdzamy, czy raport zawiera jakiekolwiek dane
                if ($report && count($report) > 0) {
                    // Wyświetlanie wyników w tabeli HTML
                    echo '<table border="1">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Marka</th>';
                    echo '<th>Model</th>';
                    echo '<th>Liczba wypożyczeń</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    // Przechodzimy po wynikach i wyświetlamy je w wierszach tabeli
                    foreach ($report as $row) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['marka']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['model']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['liczba_wypozyczen']) . '</td>';
                        echo '</tr>';
                    }

                    echo '</tbody>';
                    echo '</table>';
                } else {
                    // Jeśli brak wyników
                    echo 'Brak wyników.';
                }
            }
            ?>






        </main>


    </div>

</body>

</html>






</html>