<?php
declare(strict_types=1);

/**
 * norav4 Routes - Simple & Secure
 * Format: Router::add('gate', 'METHOD', [Controller::class, 'action'], ['auth' => true, 'role' => 'admin'])
 */

use App\Core\Router;
use App\Controllers\PublicController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;

// Public
Router::add('home', 'GET', [PublicController::class, 'home']);
Router::add('lacak', 'GET', [PublicController::class, 'tracking']);
Router::add('lacak', 'POST', [PublicController::class, 'tracking']);
Router::add('verify_tracking', 'POST', [PublicController::class, 'verifyTracking']);
Router::add('detail', 'GET', [PublicController::class, 'showDetail']);

// Auth
Router::add('login', 'GET', [AuthController::class, 'showLogin']);
Router::add('login', 'POST', [AuthController::class, 'login']);
Router::add('logout', 'GET', [AuthController::class, 'logout']);

// Dashboard (auth required)
Router::add('dashboard', 'GET', [DashboardController::class, 'index'], ['auth' => true]);
Router::add('registrasi', 'GET', [DashboardController::class, 'registrasi'], ['auth' => true]);
Router::add('registrasi/create', 'GET', [DashboardController::class, 'create'], ['auth' => true]);
Router::add('registrasi/store', 'POST', [DashboardController::class, 'store'], ['auth' => true]);
Router::add('update_status', 'POST', [DashboardController::class, 'updateStatus'], ['auth' => true]);

// Admin only
Router::add('cms', 'GET', [DashboardController::class, 'cms'], ['auth' => true, 'role' => ROLE_OWNER]);
Router::add('users', 'GET', [DashboardController::class, 'users'], ['auth' => true, 'role' => ROLE_OWNER]);

// ... tambah routes lain sesuai norav3 setelah controllers ready

