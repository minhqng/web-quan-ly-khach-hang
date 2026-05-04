<?php

declare(strict_types=1);

require_once __DIR__ . '/co-so-du-lieu.php';

function lay_ket_noi_csdl(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );

    try {
        $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . DB_CHARSET,
        ]);
        $pdo->exec('SET NAMES ' . DB_CHARSET . ' COLLATE ' . lay_collation_ket_noi($pdo));
    } catch (PDOException $exception) {
        throw new RuntimeException('Không thể kết nối cơ sở dữ liệu.', 0, $exception);
    }

    return $pdo;
}

function lay_collation_ket_noi(PDO $pdo): string
{
    $collations = [DB_COLLATION, DB_FALLBACK_COLLATION];
    $stmt = $pdo->prepare(
        'SELECT COLLATION_NAME
         FROM information_schema.COLLATIONS
         WHERE CHARACTER_SET_NAME = :charset
           AND COLLATION_NAME = :collation
         LIMIT 1'
    );

    foreach ($collations as $collation) {
        $stmt->execute([
            'charset' => DB_CHARSET,
            'collation' => $collation,
        ]);

        if ($stmt->fetchColumn()) {
            return $collation;
        }
    }

    return DB_FALLBACK_COLLATION;
}
