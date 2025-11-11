<?php

class AdminManager {
    private const ADMINS_FILE = __DIR__ . '/data/admins.json';

    public function __construct() {
        if (!file_exists(dirname(self::ADMINS_FILE))) {
            mkdir(dirname(self::ADMINS_FILE), 0777, true);
        }

        if (!file_exists(self::ADMINS_FILE)) {
            $default = [
                ['username' => 'admin', 'password' => password_hash('admin', PASSWORD_DEFAULT)]
            ];
            $this->saveAdmins($default);
        }
    }

    private function loadAdmins(): array {
        $json = file_get_contents(self::ADMINS_FILE);
        return json_decode($json, true) ?? [];
    }

    private function saveAdmins(array $admins): void {
        file_put_contents(self::ADMINS_FILE, json_encode($admins, JSON_PRETTY_PRINT));
    }

    public function verifyCredentials(string $username, string $password): bool {
        foreach ($this->loadAdmins() as $admin) {
            if ($admin['username'] === $username && password_verify($password, $admin['password'])) {
                return true;
            }
        }
        return false;
    }

    public function addAdmin(string $username, string $password): bool {
        $admins = $this->loadAdmins();
        foreach ($admins as $a) {
            if ($a['username'] === $username) return false;
        }
        $admins[] = ['username' => $username, 'password' => password_hash($password, PASSWORD_DEFAULT)];
        $this->saveAdmins($admins);
        return true;
    }

    public function removeAdmin(string $username): void {
        $admins = array_filter($this->loadAdmins(), fn($a) => $a['username'] !== $username);
        $this->saveAdmins(array_values($admins));
    }
}

?>
