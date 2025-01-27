<?php
session_start();
if (!isset($_SESSION['user']) or $_SESSION['user']['rola'] !== 'serwisant') {
    header("Location: ../index.php");
    exit();
}
?>
<?php
require_once "../users/ServiceManager.php";
$serviceManager = new ServiceManager();
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
                <li><a href="serwisant.php">Lista pojazdów floty</a></li>
            </ul>
        </aside>

        <!-- Górny pasek -->


        <header class="topbar">
            <h1>Panel Serwisanta</h1>
            <form action="" method="get" class="topbar__form">
                <h3><?php echo $_SESSION['user']['imie'] . ' ' . $_SESSION['user']['nazwisko'] ?></h3>
                <input type="submit" name="logout" value="wyloguj" class="logout" />
            </form>
        </header>
        <?php
        if (isset($_GET['logout'])) {
            unset($_SESSION['user']);
            header("Location: ../index.php");
        }
        ?>

        <!-- Obszar roboczy -->
        <main class="content">

            <?php
            if (isset($_GET['przeglad'])) {
                formularzPrzegladu($serviceManager);
            } else if (isset($_GET['serwis'])) {
                formularzSerwisowania($serviceManager);
            } else {
                mainDashboard($serviceManager);
            }
            ?>

            <?php
            function mainDashboard($serviceManager)
            {
                $vehicles = $serviceManager->getAllVehicles();

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
                        echo '<a href="?przeglad=' . htmlspecialchars($vehicle['id_pojazdu']) . '" class="edit">Dokonaj przeglądu</a> | ';
                        echo '<a href="?serwis=' . htmlspecialchars($vehicle['id_pojazdu']) . '" class="edit">Dokonaj serwisu</a> ';

                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6">Brak pojazdów do wyświetlenia</td></tr>';
                }

                echo '</tbody>';
                echo '</table>';
            }

            function formularzPrzegladu($serviceManager)
            {

                $vehicle = $serviceManager->getVehicleById($_GET['przeglad']);

                echo "<h2>Dokonaj przegladu</h2>";
                if (isset($message)) echo "<p>$message</p>";
                echo '<form action="" method="post">';
                echo "<h3>Dokonujesz przeglądu pojazdu. Ustaw datę dokonanego przeglądu pojazdu</h3>";
                echo '<label>data ostatniego przeglądu: <input required type="date" name="data_przegladu" value="' . htmlspecialchars($vehicle['data_przegladu']) . '"></label><br>';
                echo '<button type="submit" name="ustaw">Ustaw datę przeglądu</button>';
                echo '</form>';
            ?>

                <?php
                if (isset($_POST['ustaw'])) {
                    $data_przegladu = isset($_POST['data_przegladu']) ? $_POST['data_przegladu'] : null;
                    // Wywołanie metody edytowania menedżera
                    $result = $serviceManager->PrzegladPojazdu($_GET['przeglad'], $data_przegladu);
                    if ($result) {
                        echo "Data przeglądu została zmieniona";
                    } else {
                        echo "Błąd przy wprowadzaniu daty przeglądu";
                    }
                }

                ?>

            <?php
            }

            function formularzSerwisowania($serviceManager)
            {
                $vehicle = $serviceManager->getVehicleById($_GET['serwis']);

                echo "<h2>Dokonaj przegladu</h2>";
                if (isset($message)) echo "<p>$message</p>";
                echo '<form action="" method="post">';
                echo "<h3>Dokonujesz serwisu pojazdu. Wpisz dane serwioswe oraz ustaw odpowiedni status pojazdu</h3>";
                echo '<label>Dane serwisowe: <input required type="text" name="dane_serwisowe"></label><br>';

                echo '<label for="status">Status:</label>
                <select name="status" id="status">
                    <option value="dostepny"' . ($vehicle['status'] === 'dostepny' ? ' selected' : '') . '>Dostępny</option>
                    <option value="niedostepny"' . ($vehicle['status'] === 'niedostepny' ? ' selected' : '') . '>Niedostępny</option>
                    <option value="serwisowany"' . ($vehicle['status'] === 'serwisowany' ? ' selected' : '') . '>Serwisowany</option>
                </select>';

                echo '<button type="submit" name="serwis">Zapisz serwis</button>';
                echo '</form>';
            ?>

                <?php
                if (isset($_POST['serwis'])) {
                    $dane_serwisowe = isset($_POST['dane_serwisowe']) ? $_POST['dane_serwisowe'] : null;
                    $status = isset($_POST['status']) ? $_POST['status'] : null;

                    // Wywołanie metody edytowania menedżera
                    $result = $serviceManager->servieVehicle($_GET['serwis'], $dane_serwisowe, $status);
                    if ($result) {
                        echo "Dane serwisowe zostały poprawnie zmienione";
                    } else {
                        echo "Błąd przy wprowadzaniu danych!";
                    }
                }

                ?>

            <?php
            }

            ?>
        </main>
    </div>

</body>

</html>






</html>