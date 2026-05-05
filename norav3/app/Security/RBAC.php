<?php
declare(strict_types=1);

namespace App\Security;

use App\Adapters\Logger;

/**
 * SK-05: Role-Based Access Control
 * Permission mapping with wildcard access.
 */
class RBAC
{
    /**
     * Permission mapping: role => array of permissions.
     * '*' grants full access.
     */
    private static array $permissions = [
        ROLE_OWNER  => ['*'],
        ROLE_STAFF  => [
            'dashboard.view',
            'registrasi.view', 'registrasi.create', 'registrasi.edit', 'registrasi.history',
            'status.update', 'klien.update', 'kendala.toggle', 'lock.toggle',
        ],
        ROLE_PUBLIK => [
            'home.view', 'tracking.view', 'tracking.verify', 'detail.view',
        ],
    ];

    /**
     * Check if a role has a specific permission.
     */
    public static function can(string $role, string $permission): bool
    {
        if (!isset(self::$permissions[$role])) {
            return false;
        }

        $perms = self::$permissions[$role];

        // Wildcard = full access
        if (in_array('*', $perms, true)) {
            return true;
        }

        return in_array($permission, $perms, true);
    }

    /**
     * Enforce a permission. Dies with 403 if unauthorized.
     */
    public static function enforce(string $permission): void
    {
        $session = Auth::getSession();
        $role = $session['role'] ?? 'guest';

        if (!self::can($role, $permission)) {
            Logger::security('RBAC_ACCESS_DENIED', [
                'permission' => $permission,
                'role'       => $role,
                'user_id'    => $session['user_id'] ?? 'guest',
                'uri'        => $_SERVER['REQUEST_URI'] ?? '',
                'ip'         => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]);

            if (self::isAjax()) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Forbidden']);
                exit;
            }

            http_response_code(403);
            if (defined('VIEWS_PATH') && file_exists(VIEWS_PATH . '/errors/403.php')) {
                require VIEWS_PATH . '/errors/403.php';
            } else {
                echo '<h1>403 - Forbidden</h1>';
            }
            exit;
        }
    }

    /**
     * Check if current request is AJAX.
     */
    private static function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
