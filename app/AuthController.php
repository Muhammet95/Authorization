<?php

namespace App;

class AuthController
{
    public function me(): bool
    {
        // Если пользователь записан в сессии
        if (!empty($_SESSION['auth']) && $_SESSION['auth'] === true)
            return true;

        if (!empty($_COOKIE['email']) && !empty($_COOKIE['token'])) {
            $email = $_COOKIE['email'];
            $token = $_COOKIE['token'];

            $connection = (new DatabaseController())->getConnection();

            $statement = $connection->prepare("SELECT * FROM users WHERE email = :email AND remember_token = :token");
            $statement->execute([
                'email' => $email,
                'token' => $token
            ]);

            $user = $statement->fetch();
            if (!empty($user)) {
                session_start();
                $_SESSION['auth'] = true;
                $_SESSION['id'] = $user['id'];
                return true;
            }
        }

        return false;
    }

    public function login(): array
    {
        $inputJSON = file_get_contents('php://input');
        $body = json_decode($inputJSON, TRUE);
        $email = $body['email'];
        $password = $body['password'];

        if (empty($email) || empty($password))
            return [
                'status' => 'error',
                'message' => 'Электронный адрес или пароль пустые.'
            ];

        $tries = $_COOKIE['login_try'] ?? 1;

        if ($tries > 5)
            return [
                'status' => 'error',
                'message' => 'У вас слишком много ошибочных попыток. Подожди 1 минуту и заново попробуйте войти.'
            ];

        $tries ++;
        setcookie('login_try', $tries, time() + 60);

        $password = md5($password);

        $connection = (new DatabaseController())->getConnection();

        $statement = $connection->prepare('SELECT * FROM users WHERE email = :email AND password = :password');
        $statement->execute([
            'email' => $email,
            'password' => $password
        ]);

        $user = $statement->fetch();

        if (empty($user))
            return [
                'status' => 'error',
                'message' => 'Пользователь с такими данными не найден.'
            ];

        session_start();

        $_SESSION['auth'] = true;
        $_SESSION['id'] = $user['id'];

        $token = bin2hex(random_bytes(16));

        setcookie('email', $email, time()+60*60*24*30);
        setcookie('token', $token, time()+60*60*24*30);

        $connection->query("UPDATE users SET remember_token = '$token' WHERE email = '$email'");

        return [
            'status' => 'success',
            'message' => 'Пользователь успешно авторизован.'
        ];
    }

    public function logout(): bool
    {
        if (!empty($_SESSION['auth']) && $_SESSION['auth'] === true)
            session_destroy();

        setcookie('email', '', time());
        setcookie('token', '', time());

        return true;
    }
}