<?php

class MemberManager {
    private const DATA_FILE = __DIR__ . '/data/members.json';

    public function __construct() {
        if (!file_exists(dirname(self::DATA_FILE))) {
            mkdir(dirname(self::DATA_FILE), 0777, true);
        }

        if (!file_exists(self::DATA_FILE)) {
            file_put_contents(self::DATA_FILE, json_encode([]));
        }
    }

    private function loadMembers(): array {
        $json = file_get_contents(self::DATA_FILE);
        return json_decode($json, true) ?? [];
    }

    private function saveMembers(array $members): void {
        file_put_contents(self::DATA_FILE, json_encode($members, JSON_PRETTY_PRINT));
    }

    private function getNextTitheNumber(): string {
        $members = $this->loadMembers();
        if (empty($members)) return '0001';
        $max = max(array_map(fn($m) => (int) $m['tithe_number'], $members));
        return str_pad($max + 1, 4, '0', STR_PAD_LEFT);
    }

    public function addMember(array $data): void {
        $members = $this->loadMembers();
        $data['id'] = uniqid();
        $data['tithe_number'] = $this->getNextTitheNumber();
        $data['added_on'] = date('Y-m-d H:i:s');
        $defaults = [
            'nrc' => '', 'location' => '', 'position' => '', 'photo' => '',
            'date_joined' => '', 'date_baptism' => '', 'date_second_baptism' => ''
        ];
        $data = array_merge($defaults, $data);
        $members[] = $data;
        $this->saveMembers($members);
    }

    public function updateMember(string $id, array $newData): bool {
        $members = $this->loadMembers();
        foreach ($members as &$m) {
            if ($m['id'] === $id) {
                $m = array_merge($m, $newData);
                $this->saveMembers($members);
                return true;
            }
        }
        return false;
    }

    public function deleteMember(string $id): bool {
        $members = array_filter($this->loadMembers(), fn($m) => $m['id'] !== $id);
        $this->saveMembers(array_values($members));
        return true;
    }

    public function getMember(string $id): ?array {
        foreach ($this->loadMembers() as $m) {
            if ($m['id'] === $id) return $m;
        }
        return null;
    }

    public function searchMembers(string $query = ''): array {
        $members = $this->loadMembers();
        if (empty($query)) return $members;

        $query = strtolower($query);
        return array_filter($members, function ($m) use ($query) {
            return stripos($m['name'], $query) !== false
                || stripos($m['family'], $query) !== false
                || stripos($m['tithe_number'], $query) !== false
                || stripos($m['nrc'] ?? '', $query) !== false;
        });
    }

    public function getStats(): array {
        $members = $this->loadMembers();
        $zones = [];
        $ageGroups = ['0-18' => 0, '19-35' => 0, '36-50' => 0, '51+' => 0];
        $baptismYears = [];
        $familySizes = [];
        $male = $female = 0;

        foreach ($members as $m) {
            $zone = $m['zone'] ?? 'Unknown';
            $zones[$zone] = ($zones[$zone] ?? 0) + 1;

            $age = $m['age'] ?? 0;
            if ($age <= 18) $ageGroups['0-18']++;
            elseif ($age <= 35) $ageGroups['19-35']++;
            elseif ($age <= 50) $ageGroups['36-50']++;
            else $ageGroups['51+']++;

            $year = !empty($m['date_baptism']) && strtotime($m['date_baptism']) ? date('Y', strtotime($m['date_baptism'])) : null;
            if ($year) $baptismYears[$year] = ($baptismYears[$year] ?? 0) + 1;

            $size = $m['family_size'] ?? 1;
            $familySizes[$size] = ($familySizes[$size] ?? 0) + 1;

            if ($m['gender'] === 'Male') $male++;
            if ($m['gender'] === 'Female') $female++;
        }

        return [
            'total' => count($members),
            'male' => $male,
            'female' => $female,
            'zones' => $zones,
            'age_groups' => $ageGroups,
            'baptism_years' => $baptismYears,
            'family_sizes' => $familySizes
        ];
    }
}

?>
