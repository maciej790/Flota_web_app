<?php
session_start();
if (!isset($_SESSION['user']) or $_SESSION['user']['rola'] !== 'pracownik') {
    header("Location: ../index.php");
    exit();
}
require_once "../users/Employer.php";
$employer = new Employer();
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
                <li><a href="pracownik.php">Aktywne przydziały</a></li>
                <li><a href="?vehicles">Dostępne pojazdy</a></li>
            </ul>
        </aside>

        <!-- Górny pasek -->
        <header class="topbar">
            <h1>Panel Pracownika</h1>
            <form action="" method="get" class="topbar__form">
                <h3><?php echo $_SESSION['user']['imie'] . ' ' . $_SESSION['user']['nazwisko'] ?></h3>
                <input type="submit" name="logout" value="Wyloguj się" class="logout" />
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
            if (isset($_GET['vehicles'])) {
                vehicles($employer);
            } else if (isset($_GET['query'])) {
                sendQueryForm($employer);
            } else if (isset($_GET['extension'])) {
                extensionForm($employer);
            } else {
                mainDashboard($employer);
            }

            function vehicles($employer)
            {
                $avalibleCars = $employer->getAvalibleCars();

                echo '<table border="1" cellpadding="10" cellspacing="0">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Marka</th>';
                echo '<th>Model</th>';
                echo '<th>Dane serwisowe</th>';
                echo '<th>Rok produkcji</th>';
                echo '<th>Data przeglądu</th>';
                echo '<th>Przebieg</th>';
                echo '<th>Akcje</th>'; // Kolumna dla opcji
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';


                if (!empty($avalibleCars)) {
                    foreach ($avalibleCars as $car) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($car['marka']) . '</td>';
                        echo '<td>' . htmlspecialchars($car['model']) . '</td>';
                        echo '<td>' . htmlspecialchars($car['dane_serwisowe']) . '</td>';
                        echo '<td>' . htmlspecialchars($car['rok_produkcji']) . '</td>';
                        echo '<td>' . htmlspecialchars($car['data_przegladu']) . '</td>';
                        echo '<td>' . htmlspecialchars($car['przebieg']) . '</td>';
                        echo '<td>';
                        echo '<a href="?query=' . htmlspecialchars($car['id_pojazdu']) . '" class="edit">Wyślij zapytanie o pojazd</a> ';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6">Brak dostępnych pojazdów do wyświetlenia</td></tr>';
                }

                echo '</tbody>';
                echo '</table>';
            }

            function mainDashboard($employer)
            {
                $assigments = $employer->getAssigments();

                echo '<table border="1" cellpadding="10" cellspacing="0">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Marka</th>';
                echo '<th>Model</th>';
                echo '<th>Data początku wypożyczenia</th>';
                echo '<th>Data końca wypożyczenia</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';


                if (!empty($assigments)) {
                    foreach ($assigments as $assigment) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($assigment['marka']) . '</td>';
                        echo '<td>' . htmlspecialchars($assigment['model']) . '</td>';
                        echo '<td>' . htmlspecialchars($assigment['data_poczatek']) . '</td>';
                        echo '<td>' . htmlspecialchars($assigment['data_koniec']) . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6">Brak aktualnych wypożyczeń pojazdów do wyświetlenia</td></tr>';
                }

                echo '</tbody>';
                echo '</table>';
            }
            function sendQueryForm($employer)
            {

            ?>
                <div class="styled__form">
                    <h2>Stwórz zapytanie o pojazd</h2>
                    <?php if (isset($message)) echo "<p>$message</p>"; ?>
                    <form method="POST" action="">
                        <label for="imie">Data początku wynajmu:</label>
                        <input type="date" id="imie" name="poczatek" required>

                        <label for="imie">Data końca wynajmu:</label>
                        <input type="date" id="imie" name="koniec" required>

                        <label for="nazwisko">Uzasadnienie:</label>
                        <input type="text" id="nazwisko" name="uzasadnienie" required>

                        <button type="submit" name="send">Wyślij zapytania</button>
                    </form>

                    <?php
                    $poczatek = $_POST['poczatek'] ?? '';
                    $koniec = $_POST['koniec'] ?? '';
                    $uzasadnienie = $_POST['uzasadnienie'] ?? '';



                    if (isset($_POST['send'])) {
                        if ($poczatek && $koniec && $uzasadnienie) {
                            $result = $employer->sendRequest($poczatek, $koniec, $uzasadnienie, $_GET['query']);
                            if ($result) {
                                echo  "<h3 class='komunikat'>Zapytanie zostało wysłane, zostanie wkrótce rozpatrzone.</h3>";
                            } else {
                                echo  "<h3 class='error'>Wystąpił błąd podczas wysyłania zapytania.</h3>";
                            }
                        }
                    }

                    ?>

                </div>

            <?php
            }

            function extensionForm($employer) // dorobić!!!
            {
                echo "<h1>DOROBIĆ!!!</h1>";
            }
            ?>
        </main>
    </div>

</body>

</html>






</html>