<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Models\UserModel;

class AuthController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('/dashboard');
        }
        
        $title = 'Login - ' . APP_NAME;
        require VIEWS_PATH . '/auth/login.php';
    }

    public function login(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Username dan password wajib diisi.';
            redirect('/login');
        }

        $user = $this->userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password_hash'] ?? '')) {
            // Success
            Auth::login((int)$user['id'], $user['username'], $user['role']);
            redirect('/dashboard');
        } else {
            $_SESSION['error'] = 'Username atau password salah.';
            redirect('/login');
        }
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/login');
    }
}
