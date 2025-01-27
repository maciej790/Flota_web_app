<?php
require_once "../dataBase/DatabaseConnection.php";

class Employer extends DatabaseConnection
{

    public function getAssigments()
    {
        $id = $_SESSION['user']['id_osoby'];

        $sql = "
            SELECT w.*, p.marka, p.model 
            FROM wypozyczenia w 
            JOIN pojazdy p ON w.id_pojazdu = p.id_pojazdu 
            WHERE w.id_osoby = '$id';
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $assigments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $assigments;
    }

    public function getAvalibleCars()
    {
        $sql = "SELECT * FROM pojazdy WHERE status='dostepny';";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $avalibleCars = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $avalibleCars;
    }

    function sendRequest($poczatek, $koniec, $uzasadnienie, $id_pojazdu)
    {
        $id_osoby = $_SESSION['user']['id_osoby'];
        $sql1 = "SELECT COUNT(*) > 0 AS czy_wypozyczony
                FROM zapytania
                WHERE id_osoby = '$id_osoby'
                AND id_pojazdu = '$id_pojazdu'";



        $stmt1 = $this->pdo->prepare($sql1);
        $stmt1->execute();

        $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);

        if ($result1['czy_wypozyczony']) {
            echo "Błąd: Pracownik zapytał już o ten pojazd!" . "<br>";
        } else {
            if ($poczatek > $koniec) {
                echo "Data początku wypożyczenia jest dłuższa niż data końca!" . "<br>";
            } else if ($poczatek < date("Y-m-d")) {
                echo "Próbujesz wypożyczyć pojazd z datą z przeszłości!" . "<br>";
            } else {
                $sql2 = "INSERT INTO public.zapytania (
                    id_osoby, id_pojazdu, data_poczatek, data_koniec, uzasadnienie) VALUES (:id_osoby, :id_pojazdu, :data_poczatek, :data_koniec, :uzasadnienie)";
                $stmt2 = $this->pdo->prepare($sql2);
                return $stmt2->execute([
                    ':id_osoby' => $_SESSION['user']['id_osoby'],
                    ':id_pojazdu' => $id_pojazdu,
                    ':data_poczatek' => $poczatek,
                    ':data_koniec' => $koniec,
                    ':uzasadnienie' => $uzasadnienie,
                ]);
            }
        }
    }
}
