<?php

namespace Project\AluraPlay\Controller;

use PDO;
use League\Plates\Engine;
use Project\AluraPlay\Helper\FlashMessageTrait;

class LoginController implements Controller
{
    use FlashMessageTrait;

    private PDO $conn;
    private string $requestMethod;
    private Engine $template;

    public function __construct(PDO $conn, string $requestMethod, Engine $template)
    {
        $this->conn = $conn;
        $this->requestMethod = $requestMethod;
        $this->template = $template;
    }

    public function requestProcessing(): void
    {
        if ($this->requestMethod == "POST") {
            $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, "password");
            $hash_algorithm = PASSWORD_ARGON2ID;

            $query = "SELECT * FROM users WHERE email = ?";
            $stmt = $this->conn->query($query);
            $stmt->bindValue(1, $email);
            $stmt->execute();
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $userData["password"]) ?? "") {
                if (password_needs_rehash($userData["password"], $hash_algorithm)) {
                    $query = "UPDATE users SET password = ? WHERE id = ?";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindValue(1, password_hash($password, $hash_algorithm));
                    $stmt->bindValue(2, $userData["id"]);
                    $stmt->execute();
                }
                $_SESSION["authenticated"] = true;
                header("Location: /");
                exit();
            } else {
                $this->addErrorMessage("Usuário ou senha inválidos");
                header("Location: /login");
                exit();
            }
        }

        if (array_key_exists("authenticated", $_SESSION)) {
            header("Location: /");
            exit();
        }

        echo $this->template->render("login");
        
    }
}
