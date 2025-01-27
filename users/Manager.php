<?php
require_once "../dataBase/DatabaseConnection.php";

class Manager extends DatabaseConnection
{
    public function getAllEmployers()
    {
        $sql = "SELECT * FROM osoby WHERE rola='pracownik' OR rola='serwisant'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $employers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $employers;
    }

    public function addEmployer($imie, $nazwisko, $pesel, $mail, $login, $rola, $password)
    {
        $sql = "INSERT INTO osoby (imie, nazwisko, pesel, mail, rola, login, password) VALUES (:imie, :nazwisko, :pesel, :mail, :rola, :login, :password)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':imie' => $imie,
            ':nazwisko' => $nazwisko,
            ':pesel' => $pesel,
            ':mail' => $mail,
            ':login' => $login,
            ':rola' => $rola,
            ':password' => $password,
        ]);
    }

    public function deleteEmployer($id)
    {
        $sql = "DELETE FROM osoby WHERE id_osoby='$id'";
        return $this->pdo->exec($sql);
    }


    public function getEmployerById($id)
    {
        $sql = "SELECT * FROM osoby WHERE id_osoby='$id'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $employer = $stmt->fetch();

        return $employer;
    }

    public function editEmployer($id, $imie, $nazwisko, $mail, $pesel, $login, $rola, $password)
    {
        // Tworzenie zapytania SQL bez bindowania parametrów
        $sql = "UPDATE osoby 
                SET imie = '$imie', nazwisko = '$nazwisko', mail = '$mail', pesel = '$pesel', login = '$login', rola='$rola', password = '$password' 
                WHERE id_osoby = $id";

        // Wykonanie zapytania SQL
        return $this->pdo->exec($sql);
    }

    public function getAssigments()
    {
        $sql = '
        SELECT w.*, p."marka", p."model", o."imie", o."nazwisko"
        FROM "wypozyczenia" w
        JOIN "pojazdy" p ON w."id_pojazdu" = p."id_pojazdu"
        JOIN "osoby" o ON w."id_osoby" = o."id_osoby"
    ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $assigments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $assigments;
    }

    public function getRequests()
    {
        $sql = '
            SELECT 
    z.id_zapytania,
    o.imie,
    o.nazwisko,
    z.data_poczatek,
    z.data_koniec,
    p.marka,
    z.uzasadnienie,
    p.model
FROM 
    zapytania z
LEFT JOIN 
    osoby o ON z.id_osoby = o.id_osoby
LEFT JOIN 
    pojazdy p ON z.id_pojazdu = p.id_pojazdu;

    ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $requests;
    }

    public function accpetRequest($id)
    {

        $sql = <<<SQL
                DO $$
                DECLARE
                    osoba_id BIGINT;
                    pojazd_id BIGINT;
                    zap_data_poczatek DATE;
                    zap_data_koniec DATE;
                BEGIN
                    -- Pobierz dane z tabeli zapytania
                    SELECT z.id_osoby, z.id_pojazdu, z.data_poczatek, z.data_koniec
                    INTO osoba_id, pojazd_id, zap_data_poczatek, zap_data_koniec
                    FROM zapytania z
                    WHERE z.id_zapytania = $id;

                    -- Zaktualizuj status pojazdu na 'wypozyczony'
                    UPDATE pojazdy
                    SET status = 'wypozyczony'
                    WHERE id_pojazdu = pojazd_id;

                    UPDATE zapytania
                    SET decyzja = 't'
                    WHERE id_pojazdu = pojazd_id;


                    -- Wstaw nowy rekord do tabeli wypozyczenia
                    INSERT INTO wypozyczenia (id_osoby, id_pojazdu, data_poczatek, data_koniec)
                    VALUES (osoba_id, pojazd_id, zap_data_poczatek, zap_data_koniec);

                    -- Usuń zapytanie z tabeli zapytania
                    DELETE FROM zapytania
                    WHERE id_zapytania = $id;
                END $$;
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
    }

    public function declineRequest($id)
    {

        $sql = <<<SQL
                DO $$
        DECLARE
            pojazd_id BIGINT;
        BEGIN
            -- Pobierz ID pojazdu na podstawie ID przydziału
            SELECT id_pojazdu
            INTO pojazd_id
            FROM wypozyczenia
            WHERE id_wypozyczenia = $id;

            UPDATE zapytania
            SET decyzja = 'f'
            WHERE id_pojazdu = pojazd_id;

            DELETE FROM zapytania
            WHERE id_zapytania = $id;
        END $$;
                   
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
    }


    public function deleteAsigment($id)
    {

        $sql = <<<SQL
        DO $$
        DECLARE
            pojazd_id BIGINT;
        BEGIN
            -- Pobierz ID pojazdu na podstawie ID przydziału
            SELECT id_pojazdu
            INTO pojazd_id
            FROM wypozyczenia
            WHERE id_wypozyczenia = $id;
        
            -- Zaktualizuj status pojazdu na 'dostępny'
            UPDATE pojazdy
            SET status = 'dostepny'
            WHERE id_pojazdu = pojazd_id;
        
            -- Usuń wpis z tabeli wypozyczenia
            DELETE FROM wypozyczenia
            WHERE id_wypozyczenia = $id;
        END $$;
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
    }

    public function getAllVehicles()
    {
        $sql = "SELECT * FROM pojazdy";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $vehicles;
    }

    function getVehicelById($id)
    {
        $sql = "SELECT * FROM pojazdy WHERE id_pojazdu = '$id'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $vehicles = $stmt->fetch();

        return $vehicles;
    }

    public function editVehicle($id, $marka, $model, $dane_serwisowe, $status, $data_przegladu, $przebieg)
    {
        $sql = "UPDATE pojazdy 
                SET marka = '$marka', model = '$model', dane_serwisowe = '$dane_serwisowe', status = '$status', data_przegladu = '$data_przegladu', przebieg='$przebieg' 
                WHERE id_pojazdu = $id";


        return $this->pdo->exec($sql);
    }



    public function deleteVehilce($vehicleId)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM pojazdy WHERE id_pojazdu = ?");
            $stmt->execute([$vehicleId]);
        } catch (PDOException $e) {
            // Sprawdzenie, czy błąd dotyczy klucza obcego
            if ($e->getCode() === '23503') {
                throw new Exception("Pojazd w użyciu, najpierw zakończ przydział.");
            }
            // Rzucenie innych wyjątków, jeśli wystąpiły
            throw $e;
        }
    }

    public function addVehicle($marka, $model, $dane_serwisowe, $rok_produkcji, $status, $data_przegladu)
    {
        $sql = "INSERT INTO pojazdy (marka, model, dane_serwisowe, rok_produkcji, status, data_przegladu) VALUES (:marka, :model, :dane_serwisowe, :rok_produkcji, :status, :data_przegladu)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':marka' => $marka,
            ':model' => $model,
            ':dane_serwisowe' => $dane_serwisowe,
            ':rok_produkcji' => $rok_produkcji,
            ':status' => $status,
            ':data_przegladu' => $data_przegladu,
        ]);
    }

    public function generateReport()
    {
        $sql = "SELECT p.\"marka\", p.\"model\", COUNT(w.\"id_wypozyczenia\") AS \"liczba_wypozyczen\" 
                FROM \"pojazdy\" p 
                LEFT JOIN \"wypozyczenia\" w ON p.\"id_pojazdu\" = w.\"id_pojazdu\" 
                GROUP BY p.\"id_pojazdu\", p.\"marka\", p.\"model\";";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $reportTable = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $reportTable;
    }
}
