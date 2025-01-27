<?php

class DatabaseConnection
{
    public $pdo;

    // Konstruktor inicjujący połączenie z bazą danych
    public function __construct()
    {
        // Dane do połączenia z lokalną bazą danych
        $host = "localhost";
        $port = "5432";
        $dbname = "flota_utf8_win1250";
        $user = "postgres";
        $password = "postgres";

        // Tworzenie DSN (Data Source Name)
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

        try {
            // Inicjalizujemy połączenie PDO w momencie tworzenia obiektu
            $this->pdo = new PDO($dsn, $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new PDOException("Nie udało się połączyć z bazą danych: " . $e->getMessage());
        }


        // $uri = "postgres://avnadmin:AVNS_nwE4HaCLlsPnTIqF0Xt@pg-31dd3fb6-student-d817.b.aivencloud.com:26836/defaultdb?sslmode=require";

        // $fields = parse_url($uri);

        // // build the DSN including SSL settings
        // $conn = "pgsql:";
        // $conn .= "host=" . $fields["host"];
        // $conn .= ";port=" . $fields["port"];;
        // $conn .= ";dbname=flota_utf8_win1250";
        // $conn .= ";sslrootcert=" . __DIR__ . "/../../ca.pem";


        // try {
        //     $this->pdo = new PDO($conn, $fields["user"], $fields["pass"]);
        //     $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // } catch (PDOException $e) {
        //     throw new PDOException("Nie udało się połączyć z bazą danych: " . $e->getMessage());
        // }
    }
}
