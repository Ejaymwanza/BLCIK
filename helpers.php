<?php

// Constants
define('DATA_FILE', __DIR__ . '/data/members.json');
define('ADMINS_FILE', __DIR__ . '/data/admins.json');
define('HOUSEHOLDS_FILE', __DIR__ . '/data/households.json');

// Ensure data directory exists
if (!file_exists(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0777, true);
}

// --------------------
// Admin Functions
// --------------------

function load_admins() {
    if (!file_exists(ADMINS_FILE)) {
        $default_admins = [
            ['username' => 'admin', 'password' => password_hash('admin', PASSWORD_DEFAULT)]
        ];
        file_put_contents(ADMINS_FILE, json_encode($default_admins, JSON_PRETTY_PRINT));
    }
    return json_decode(file_get_contents(ADMINS_FILE), true);
}

function save_admins($admins) {
    file_put_contents(ADMINS_FILE, json_encode($admins, JSON_PRETTY_PRINT));
}

function verify_admin_credentials($username, $password) {
    foreach (load_admins() as $admin) {
        if ($admin['username'] === $username && password_verify($password, $admin['password'])) {
            return true;
        }
    }
    return false;
}

function add_admin($username, $password) {
    $admins = load_admins();
    foreach ($admins as $a) {
        if ($a['username'] === $username) return false;
    }
    $admins[] = ['username' => $username, 'password' => password_hash($password, PASSWORD_DEFAULT)];
    save_admins($admins);
    return true;
}

function remove_admin($username) {
    $admins = array_filter(load_admins(), fn($a) => $a['username'] !== $username);
    save_admins(array_values($admins));
}

// --------------------
// Member Functions
// --------------------

function load_members() {
    if (!file_exists(DATA_FILE)) file_put_contents(DATA_FILE, json_encode([]));
    $members = json_decode(file_get_contents(DATA_FILE), true);
    return is_array($members) ? $members : [];
}

function save_members($members) {
    file_put_contents(DATA_FILE, json_encode($members, JSON_PRETTY_PRINT));
}

function get_next_tithe_number() {
    $members = load_members();
    $existingNumbers = array_column($members, 'tithe_number');
    $maxNum = 0;
    foreach ($existingNumbers as $num) {
        // Only consider numeric tithe numbers (skip household numbers that may not be pure tithe numbers)
        if (is_numeric($num) && intval($num) > $maxNum) {
            $maxNum = intval($num);
        }
    }
    $next = $maxNum + 1;
    return str_pad($next, 4, '0', STR_PAD_LEFT);
}

function add_member($data) {
    $members = load_members();
    $data['tithe_number'] = get_next_tithe_number();
    $data['id'] = uniqid();
    $data['added_on'] = date('Y-m-d H:i:s');

    // Optional fields
    $fields = ['nrc', 'location', 'position', 'photo', 'date_joined', 'date_baptism', 'date_second_baptism', 'household_id', 'gender', 'dob', 'phone', 'role', 'zone', 'ministry', 'family', 'age'];
    foreach ($fields as $field) {
        $data[$field] = $data[$field] ?? '';
    }

    $members[] = $data;
    save_members($members);
}

// --------------------
// Household Functions
// --------------------

function load_households() {
    if (!file_exists(HOUSEHOLDS_FILE)) file_put_contents(HOUSEHOLDS_FILE, json_encode([]));
    $households = json_decode(file_get_contents(HOUSEHOLDS_FILE), true);
    return is_array($households) ? $households : [];
}

function save_households($households) {
    file_put_contents(HOUSEHOLDS_FILE, json_encode($households, JSON_PRETTY_PRINT));
}

/**
 * Add household with household number formed by combining house_number + tithe_number
 * Requires 'household_number', 'household_name', 'address', 'contact_number', 'family_members' in $data
 */
function add_household($data) {
    $members = load_members();
    $households = load_households();

    $household_number = $data['household_number']; // e.g., 21001

    // Check if household number already exists to avoid duplicates (should be done before calling this function)
    if (household_number_exists($household_number)) {
        throw new Exception("Household number $household_number already exists.");
    }

    $timestamp = date('Y-m-d H:i:s');

    // Save household meta
    $households[] = [
        'household_number' => $household_number,
        'household_name' => $data['household_name'],
        'address' => $data['address'],
        'contact_number' => $data['contact_number'],
        'created_at' => $timestamp
    ];
    save_households($households);

    // Add household head as member (with role 'Household Head')
    $members[] = [
        'id' => uniqid(),
        'name' => $data['household_name'],
        'tithe_number' => '',  // This can be empty or generated separately if needed
        'gender' => 'N/A',
        'dob' => '',
        'role' => 'Household Head',
        'household_id' => $household_number,
        'added_on' => $timestamp,
        'nrc' => '',
        'phone' => $data['contact_number'],
        'location' => $data['address'],
        'zone' => '',
        'ministry' => '',
        'family' => '',
        'position' => '',
        'age' => '',
        'photo' => '',
        'date_joined' => '',
        'date_baptism' => '',
        'date_second_baptism' => ''
    ];

    // Add family members
    if (!empty($data['family_members']) && is_array($data['family_members'])) {
        foreach ($data['family_members'] as $member) {
            $members[] = [
                'id' => uniqid(),
                'name' => $member['name'],
                'tithe_number' => '', // or generate if needed
                'gender' => $member['gender'] ?? 'N/A',
                'dob' => $member['dob'] ?? '',
                'role' => 'Family Member',
                'household_id' => $household_number,
                'added_on' => $timestamp,
                'nrc' => '',
                'phone' => '',
                'location' => $data['address'],
                'zone' => '',
                'ministry' => '',
                'family' => '',
                'position' => '',
                'age' => '',
                'photo' => '',
                'date_joined' => '',
                'date_baptism' => '',
                'date_second_baptism' => ''
            ];
        }
    }

    save_members($members);
}

/**
 * Checks if a household number already exists
 * @param string $household_number
 * @return bool
 */
function household_number_exists(string $household_number): bool {
    $households = load_households();
    foreach ($households as $household) {
        if (($household['household_number'] ?? '') === $household_number) {
            return true;
        }
    }
    return false;
}

// --------------------
// Utility Functions
// --------------------

function get_member($id) {
    foreach (load_members() as $m) {
        if ($m['id'] === $id) return $m;
    }
    return null;
}

function search_members($query = '') {
    $query = strtolower($query);
    return array_filter(load_members(), function ($m) use ($query) {
        return strpos(strtolower($m['name']), $query) !== false
            || strpos(strtolower($m['family'] ?? ''), $query) !== false
            || strpos(strtolower($m['tithe_number'] ?? ''), $query) !== false
            || strpos(strtolower($m['nrc'] ?? ''), $query) !== false;
    });
}

// --------------------
// Stats & Reports
// --------------------

function get_total_members() {
    return count(load_members());
}

function get_male_members() {
    return count(array_filter(load_members(), fn($m) => ($m['gender'] ?? '') === 'Male'));
}

function get_female_members() {
    return count(array_filter(load_members(), fn($m) => ($m['gender'] ?? '') === 'Female'));
}

function get_members_by_zone() {
    $zones = [];
    foreach (load_members() as $member) {
        $zone = $member['zone'] ?? 'Unknown';
        $zones[$zone] = ($zones[$zone] ?? 0) + 1;
    }
    return $zones;
}

function get_age_groups() {
    $ageGroups = ['0-18' => 0, '19-35' => 0, '36-50' => 0, '51+' => 0];
    foreach (load_members() as $m) {
        $age = (int)($m['age'] ?? 0);
        if ($age <= 18) $ageGroups['0-18']++;
        elseif ($age <= 35) $ageGroups['19-35']++;
        elseif ($age <= 50) $ageGroups['36-50']++;
        else $ageGroups['51+']++;
    }
    return $ageGroups;
}

function get_baptism_years() {
    $years = [];
    foreach (load_members() as $m) {
        if (!empty($m['date_baptism']) && strtotime($m['date_baptism'])) {
            $year = date('Y', strtotime($m['date_baptism']));
            $years[$year] = ($years[$year] ?? 0) + 1;
        }
    }
    return $years;
}

function get_family_sizes() {
    $households = [];
    foreach (load_members() as $m) {
        $hid = $m['household_id'] ?? null;
        if ($hid) $households[$hid] = ($households[$hid] ?? 0) + 1;
    }

    $sizes = [];
    foreach ($households as $count) {
        $sizes[$count] = ($sizes[$count] ?? 0) + 1;
    }
    return $sizes;
}

