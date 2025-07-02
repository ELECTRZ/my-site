<?php
date_default_timezone_set('Europe/Moscow');

// Подключение к MySQL
$host = 'localhost';
$dbname = 'swarmclan_db';
$username = 'swarmclan_user';
$password = 'c467yz';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('❌ Ошибка подключения к базе данных: ' . $e->getMessage());
}

// Получение лидеров
$stmt = $pdo->query("SELECT * FROM replays ORDER BY p1_power DESC LIMIT 50");
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Пагинация
$perPage = 50;
$totalPlayers = count($players);
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($page - 1) * $perPage;
$slice = array_slice($players, $start, $perPage);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Клан SWARM | Лидеры Tekken 8</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #1a1a1a;
            color: #f0f0f0;
        }
        h1, h2 {
            text-align: center;
        }
        .header {
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 2em;
            margin: 0;
        }
        .header h2 {
            font-size: 1.2em;
            margin: 0;
            color: #aaa;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #2c2c2c;
            color: #fff;
        }
        th, td {
            padding: 12px;
            border: 1px solid #444;
            text-align: left;
        }
        th {
            background-color: #3a3a3a;
        }
        form {
            text-align: right;
            margin-bottom: 30px;
        }
        input[type="text"] {
            padding: 10px;
            width: 300px;
            font-size: 16px;
        }
        button {
            padding: 10px 15px;
            font-size: 16px;
        }
        .no-results {
            text-align: center;
            color: red;
            font-weight: bold;
        }
        .pagination {
            text-align: right;
            margin-top: 20px;
        }
        .pagination a, .pagination span {
            margin: 0 5px;
            text-decoration: none;
            color: #00bfff;
        }
        .pagination .current {
            font-weight: bold;
            color: white;
        }
    </style>
</head>
<body>

<h2>📊 Таблица лидеров</h2>
<table>
    <tr>
        <th>Место</th>
        <th>Имя</th>
        <th>Power</th>
        <th>Ранг</th>
        <th>Активность</th>
    </tr>
    <?php foreach ($slice as $key => $player): ?>
        <tr>
            <td><?= $start + $key + 1 ?></td>
            <td><?= htmlspecialchars($player['p1_name'] ?? 'Неизвестный игрок', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= $player['p1_power'] ?? 0 ?></td>
            <td><?= htmlspecialchars($rankMap[(string)($player['p1_rank'] ?? '0')] ?? 'Неизвестен', ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= date('d.m.Y H:i', $player['battle_at'] ?? time()) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>