<?php
// Script to migrate and seed database/database.sqlite from current MySQL or defaults

$sqlitePath = dirname(__DIR__) . '/database/database.sqlite';
if (file_exists($sqlitePath)) {
    unlink($sqlitePath);
}
touch($sqlitePath);

// 1. Initialize SQLite schema
putenv("DB_CONNECTION=sqlite");
putenv("DB_DATABASE={$sqlitePath}");
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = $sqlitePath;
$_SERVER['DB_CONNECTION'] = 'sqlite';
$_SERVER['DB_DATABASE'] = $sqlitePath;

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require_once dirname(__DIR__) . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "1. Running migrations on database/database.sqlite..." . PHP_EOL;
$status = $kernel->call('migrate', ['--force' => true]);
echo "Migrate result: " . $status . PHP_EOL;

// 2. Connect to MySQL to read data
$mysqlPdo = null;
try {
    $mysqlPdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=sd_inventory', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "2. Connected to local MySQL successfully." . PHP_EOL;
} catch (\Throwable $e) {
    echo "2. Could not connect to local MySQL: " . $e->getMessage() . PHP_EOL;
}

$sqlitePdo = new PDO("sqlite:{$sqlitePath}", null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$tables = [
    'users',
    'categories',
    'products',
    'customers',
    'delivery_agents',
    'suppliers',
    'coupons',
    'business_settings',
    'orders',
    'order_items',
    'deliveries',
    'order_payments',
    'activity_logs',
];

if ($mysqlPdo) {
    echo "3. Copying data from MySQL to SQLite..." . PHP_EOL;
    foreach ($tables as $table) {
        try {
            $stmt = $mysqlPdo->query("SELECT * FROM `{$table}`");
            $rows = $stmt->fetchAll();
            if (empty($rows)) {
                continue;
            }

            $firstRow = $rows[0];
            $columns = array_keys($firstRow);
            $colList = implode(', ', array_map(fn($c) => "`{$c}`", $columns));
            $placeholders = implode(', ', array_fill(0, count($columns), '?'));

            $insertSql = "INSERT OR REPLACE INTO `{$table}` ({$colList}) VALUES ({$placeholders})";
            $insertStmt = $sqlitePdo->prepare($insertSql);

            $sqlitePdo->beginTransaction();
            foreach ($rows as $row) {
                $insertStmt->execute(array_values($row));
            }
            $sqlitePdo->commit();
            echo "   - Table `{$table}`: copied " . count($rows) . " rows." . PHP_EOL;
        } catch (\Throwable $e) {
            if ($sqlitePdo->inTransaction()) {
                $sqlitePdo->rollBack();
            }
            echo "   - Table `{$table}` error: " . $e->getMessage() . PHP_EOL;
        }
    }
}

// 4. Ensure admin user tabassum.aktar24@gmail.com exists with password #ayaan24
$adminStmt = $sqlitePdo->prepare("SELECT * FROM users WHERE email = ?");
$adminStmt->execute(['tabassum.aktar24@gmail.com']);
$admin = $adminStmt->fetch();

$hashedPassword = password_hash('#ayaan24', PASSWORD_BCRYPT, ['cost' => 12]);

if ($admin) {
    $updateStmt = $sqlitePdo->prepare("UPDATE users SET password = ?, role = 'admin', status = 1 WHERE email = ?");
    $updateStmt->execute([$hashedPassword, 'tabassum.aktar24@gmail.com']);
    echo "4. Admin user updated with password '#ayaan24'." . PHP_EOL;
} else {
    $insertStmt = $sqlitePdo->prepare("INSERT INTO users (name, email, password, role, status, created_at, updated_at) VALUES (?, ?, ?, 'admin', 1, datetime('now'), datetime('now'))");
    $insertStmt->execute(['Tabassum Aktar', 'tabassum.aktar24@gmail.com', $hashedPassword]);
    echo "4. Admin user inserted with password '#ayaan24'." . PHP_EOL;
}

echo "Database SQLite sync complete! Size: " . filesize($sqlitePath) . " bytes." . PHP_EOL;
