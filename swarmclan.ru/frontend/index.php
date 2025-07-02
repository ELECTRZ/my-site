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

// Словарь рангов
$rankMap = [
    "0" => "Beginner",
    "1" => "1st Dan",
    "2" => "2nd Dan",
    "3" => "Fighter",
    "4" => "Strategist",
    "5" => "Combatant",
    "6" => "Brawler",
    "7" => "Ranger",
    "8" => "Cavalry",
    "9" => "Warrior",
    "10" => "Assailant",
    "11" => "Dominator",
    "12" => "Vanquisher",
    "13" => "Destroyer",
    "14" => "Eliminator",
    "15" => "Garyu",
    "16" => "Shinryu",
    "17" => "Tenryu",
    "18" => "Mighty Ruler",
    "19" => "Flame Ruler",
    "20" => "Battle Ruler",
    "21" => "Fujin",
    "22" => "Raijin",
    "23" => "Kishin",
    "24" => "Bushin",
    "25" => "Tekken King",
    "26" => "Tekken Emperor",
    "27" => "Tekken God",
    "28" => "Tekken God Supreme",
    "29" => "God of Destruction",
    "100" => "God of Destruction"
];

// Обработка поиска
$searchQuery = '';
$isSearch = false;
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = '%' . $_GET['search'] . '%';
    $stmt = $pdo->prepare("SELECT * FROM replays 
                          WHERE (p1_name LIKE :search OR p1_polaris_id LIKE :search) 
                          AND p1_lang = 'ru' 
                          ORDER BY p1_power DESC");
    $stmt->bindParam(':search', $searchTerm);
    $stmt->execute();
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $isSearch = true;
} else {
    // Получение лидеров (только русскоязычные)
    $stmt = $pdo->query("SELECT * FROM replays WHERE p1_lang = 'ru' ORDER BY p1_power DESC LIMIT 100");
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

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
            background-color: #00bfff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #009acd;
        }
        .no-results {
            text-align: center;
            color: red;
            font-weight: bold;
            padding: 20px;
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a, .pagination span {
            margin: 0 5px;
            text-decoration: none;
            color: #00bfff;
            padding: 5px 10px;
            border: 1px solid #444;
        }
        .pagination a:hover {
            background-color: #333;
        }
        .pagination .current {
            font-weight: bold;
            color: white;
            background-color: #00bfff;
        }
        .search-info {
            text-align: center;
            margin: 20px 0;
            color: #aaa;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Клан SWARM</h1>
    <h2>Рейтинг игроков Tekken 8</h2>
</div>

<!-- Форма поиска -->
<form action="" method="get">
    <input type="text" name="search" placeholder="Введите ник или Tekken ID" 
           value="<?= htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <button type="submit">Поиск</button>
    <?php if ($isSearch): ?>
        <a href="?" style="margin-left: 10px;">Сбросить</a>
    <?php endif; ?>
</form>

<?php if ($isSearch): ?>
    <div class="search-info">
        Результаты поиска по запросу: "<strong><?= htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') ?></strong>"
    </div>
<?php endif; ?>

<h2>📊 Таблица лидеров</h2>
<?php if (empty($slice)): ?>
    <div class="no-results">Игроки не найдены</div>
<?php else: ?>
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
                <td><?= number_format($player['p1_power'] ?? 0, 0, '', ' ') ?></td>
                <td><?= htmlspecialchars($rankMap[(string)($player['p1_rank'] ?? '0')] ?? 'Неизвестен', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= date('d.m.Y H:i', $player['battle_at'] ?? time()) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Пагинация -->
    <?php if (!$isSearch && $totalPlayers > $perPage): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>">← Назад</a>
            <?php endif; ?>
            
            <span class="current">Страница <?= $page ?></span>
            
            <?php if ($start + $perPage < $totalPlayers): ?>
                <a href="?page=<?= $page + 1 ?>">Вперед →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>