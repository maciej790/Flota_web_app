<?php
class User extends DatabaseConnection
{
    public $userData;

    public function signIn($login, $password)
    {

        parent::__construct();
        $sql = "SELECT * FROM osoby WHERE login = :login AND password = :password";

        try {
            // Przygotowanie zapytania z użyciem parametrów
            $stmt = $this->pdo->prepare($sql);

            // Przekazanie parametrów do zapytania
            $stmt->execute([
                ':login' => $login,
                ':password' => $password
            ]);

            // Pobranie wyniku
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userData) {
                $this->userData = $userData;
                $_SESSION['user'] = $this->userData;

                switch ($this->userData['rola']) {
                    case "admin":
                        header("Location: ./views/admin.php");
                        break;
                    case "kierownik":
                        header("Location: ./views/kierownik.php");
                        break;
                    case "serwisant":
                        header("Location: ./views/serwisant.php");
                        break;
                    case "pracownik":
                        header("Location: ./views/pracownik.php");
                        break;
                }
            } else {
                echo "Błędny login lub hasło!";
                unset($_SESSION['user']);
            }
        } catch (PDOException $e) {
            throw new Exception("Błąd zapytania: " . $e->getMessage());
        }
    }
}
