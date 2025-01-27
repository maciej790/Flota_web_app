<?php
require_once "../dataBase/DatabaseConnection.php";

class ServiceManager extends DatabaseConnection
{

    public function getAllVehicles()
    {
        $sql = "SELECT * FROM pojazdy";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $Vevicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $Vevicles;
    }

    public function getVehicleById($id)
    {
        $sql = "SELECT * FROM pojazdy WHERE id_pojazdu = '$id'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $vehicle = $stmt->fetch();

        return $vehicle;
    }

    public function PrzegladPojazdu($id, $data_przegladu)
    {
        $sql = "UPDATE pojazdy
                SET data_przegladu = '$data_przegladu' -- Podaj tutaj nową datę przeglądu
                WHERE id_pojazdu = '$id'; -- Podaj tutaj ID pojazdu, który chcesz zaktualizować
                ";
        return $this->pdo->exec($sql);
    }

    public function servieVehicle($id, $dane_serwisowe, $status)
    {
        $sql = "UPDATE pojazdy
                SET dane_serwisowe = '$dane_serwisowe', status = '$status' -- Podaj tutaj nową datę przeglądu
                WHERE id_pojazdu = '$id'; -- Podaj tutaj ID pojazdu, który chcesz zaktualizować
                ";

        return $this->pdo->exec($sql);
    }
}
