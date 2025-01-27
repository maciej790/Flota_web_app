<?php
require_once "../dataBase/DatabaseConnection.php";
class Admin extends DatabaseConnection
{
    public function getAllManagers()
    {
        $sql = "SELECT * FROM osoby WHERE rola='kierownik'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $managers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $managers;
    }

    public function getManagerById($id)
    {
        $sql = "SELECT * FROM osoby WHERE id_osoby='$id'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $manager = $stmt->fetch();

        return $manager;
    }


    public function addManager($imie, $nazwisko, $pesel, $mail, $login, $password)
    {
        $sql = "INSERT INTO osoby (imie, nazwisko, pesel, mail, rola, login, password) VALUES (:imie, :nazwisko, :pesel, :mail, 'kierownik', :login, :password)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':imie' => $imie,
            ':nazwisko' => $nazwisko,
            ':pesel' => $pesel,
            ':mail' => $mail,
            ':login' => $login,
            ':password' => $password,
        ]);

        echo $sql;
    }


    public function editManager($id, $imie, $nazwisko, $mail, $pesel, $login, $password)
    {
        // Tworzenie zapytania SQL bez bindowania parametrów
        $sql = "UPDATE osoby 
                SET imie = '$imie', nazwisko = '$nazwisko', mail = '$mail', pesel = '$pesel', login = '$login', password = '$password' 
                WHERE id_osoby = $id";

        // Wykonanie zapytania SQL
        return $this->pdo->exec($sql);
    }

    public function deleteManager($id)
    {
        $sql = "DELETE FROM osoby WHERE id_osoby='$id'";
        return $this->pdo->exec($sql);
    }
}
