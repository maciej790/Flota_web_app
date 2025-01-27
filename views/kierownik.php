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
                <input type="submit" name="logout" value="wyloguj" class="logout" />
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
                            echo  "Pracownik został dodany pomyślnie.";
                        } else {
                            echo  "Wystąpił błąd podczas dodawania Pracownika.";
                        }
                    }
                }

                ?>

            <?php
            }
            function editEmployerForm($manager)
            {
            ?>
                <?php
                $employer = $manager->getEmployerById($_GET['edit']);
                ?>
                <h2>Edycja danych Pracownika</h2>
                <?php if (isset($message)) echo "<p>$message</p>"; ?>
                <?php
                echo '<h2>Edytuj pracownika</h2>';
                echo '<form action="" method="post">';
                echo '<input type="hidden" name="id_osoby" value="' . htmlspecialchars($employer['id_osoby']) . '" required>';
                echo '<label>Imię: <input type="text" name="imie" value="' . htmlspecialchars($employer['imie']) . '" required></label><br>';
                echo '<label>Nazwisko: <input type="text" name="nazwisko" required value="' . htmlspecialchars($employer['nazwisko']) . '"></label><br>';
                echo '<label>PESEL: <input required type="number" name="pesel" value="' . htmlspecialchars($employer['pesel']) . '"></label><br>';
                echo '<label>Mail: <input  required type="email" name="mail" value="' . htmlspecialchars($employer['mail']) . '"></label><br>';
                echo '<label for="rola">Rola:</label>
                <select name="rola" id="rola">
                    <option value="pracownik"' . ($employer['rola'] === 'pracownik' ? ' selected' : '') . '>pracownik</option>
                    <option value="serwisant"' . ($employer['rola'] === 'serwisant' ? ' selected' : '') . '>serwisant</option>
                </select>';
                echo '<br>';
                echo '<label>Login: <input type="text" name="login" value="' . htmlspecialchars($employer['login']) . '"></label><br>';
                echo '<label>Hasło: <input type="password" name="password" value="' . htmlspecialchars($employer['password']) . '"></label><br>';
                echo '<button type="submit" name="edit_manager">Zapisz zmiany</button>';
                echo '</form>';
                ?>

                <?php
                if (isset($_POST['edit_manager'])) {
                    $imie = isset($_POST['imie']) ? $_POST['imie'] : null;
                    $nazwisko = isset($_POST['nazwisko']) ? $_POST['nazwisko'] : null;
                    $mail = isset($_POST['pesel']) ? $_POST['pesel'] : null;
                    $pesel = isset($_POST['mail']) ? $_POST['mail'] : null;
                    $login = isset($_POST['login']) ? $_POST['login'] : null;
                    $rola = isset($_POST['rola']) ? $_POST['rola'] : null;
                    $password = isset($_POST['password']) ? $_POST['password'] : null;

                    // Wywołanie metody edytowania menedżera
                    $result = $manager->editEmployer($_GET['edit'], $imie, $nazwisko, $mail, $pesel, $login, $rola, $password);
                    if ($result) {
                        echo "Dane Pracownika zostały zaaktualizowane!";
                    } else {
                        echo "Dane zostały błędnie wprowadzone, proszę je poprawić!";
                    }
                }

                ?>

            <?php

            }
            function deleteEmployerForm($manager)
            {
            ?>
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
                    $manager->deleteEmployer($_GET['delete']);
                    header('Location:admin.php');
                } else if (isset($_POST['delete-false'])) {
                    header('Location:admin.php');
                }


                ?>


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
                <h2>Edycja danych pojazdu</h2>
                <?php if (isset($message)) echo "<p>$message</p>"; ?>
                <?php
                echo '<h2>Edytuj dane pojazdu</h2>';
                echo '<form action="" method="post">';
                echo '<label>Marka: <input type="text" name="marka" required value="' . htmlspecialchars($vehicle['marka']) . '"></label><br>';
                echo '<label>Model: <input type="text" required name="model" value="' . htmlspecialchars($vehicle['model']) . '"></label><br>';
                echo '<label>Dane serwisowe: <input type="text" required name="dane_serwisowe" value="' . htmlspecialchars($vehicle['dane_serwisowe']) . '"></label><br>';
                echo '<label>Rok produkcji: <input type="date" required name="naped" value="' . htmlspecialchars($vehicle['rok_produkcji']) . '"></label><br>';
                echo '<label for="rola">Status:</label>
                <select name="status" id="rola" required>
                    <option value="dostepny"' . ($vehicle['status'] === 'dostepny' ? ' selected' : '') . '>Dostępny</option>
                    <option value="niedostepny"' . ($vehicle['status'] === 'niedostepny' ? ' selected' : '') . '>Niedostępny</option>
                    <option value="serwisowany"' . ($vehicle['status'] === 'serwisowany' ? ' selected' : '') . '>Serwisowany</option>
                </select>';
                echo '<br>';
                echo '<label>data przeglądu: <input required type="date" name="data_przegladu" value="' . htmlspecialchars($vehicle['data_przegladu']) . '"></label><br>';
                echo '<label>przebieg: <input required type="number" name="przebieg" value="' . htmlspecialchars($vehicle['przebieg']) . '"></label><br>';
                echo '<button type="submit" name="edit_manager">Zapisz zmiany</button>';
                echo '</form>';
                ?>

                <?php
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
                        echo "Dane pojazdu zostały zaaktualizowane!";
                    } else {
                        echo "Dane zostały błędnie wprowadzone, proszę je poprawić!";
                    }
                }

                ?>

            <?php

            }


            function removeVehicle($manager)
            {
            ?>
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
                        echo "<div class='error-message'>Pojazd jest przydzielony lub jest w zapytaniu u któregoś z pracowników, najpierw zakończ przydział lub odrzuć zapytanie!.</div>";
                    }
                } else if (isset($_POST['delete-false'])) {
                    header('Location: kierownik.php?pojazdy ');
                }


                ?>


            <?php
            }

            function addVehicle($manager)
            {
            ?>
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
                            echo  "Pojazd został dodany pomyślnie.";
                        } else {
                            echo  "Wystąpił błąd podczas dodawania pojazdu.";
                        }
                    }
                }

                ?>



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