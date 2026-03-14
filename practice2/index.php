<?php

$apiKey = '26e57c5c99e6832f7537d943adb2e2961ed63436';

$search = '';
$items = [];

if(isset($_GET['search']) && !empty($_GET['search'])){
    $search = $_GET['search'];

    $url = 'https://google.serper.dev/search';

    $data = json_encode([
        'q' => $search,
        'num' => 10
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-API-KEY: ' . $apiKey,
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $result = curl_exec($ch);
    curl_close($ch);

    $resultArray = json_decode($result, true);

    if(isset($resultArray['organic'])){
        $items = $resultArray['organic'];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h2{
            margin-bottom: 20px;
        }

        form{
            margin-bottom: 30px;
        }

        input[type="text"]{
            padding: 8px;
            width: 300px;
            border: 1px solid #ccc;
        }

        input[type="submit"]{
            padding: 8px 16px;
            background: #4285f4;
            color: white;
            border: none;
            cursor: pointer;
        }

        .result{
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .result a{
            font-size: 18px;
            color: #1a0dab;
            text-decoration: none;
        }

        .result a:hover{
            text-decoration: underline;
        }

        .result-url{
            color: green;
            font-size: 13px;
        }

        .result-desc{
            font-size: 14px;
            color: #444;
            margin-top: 4px;
        }
    </style>
</head>
<body>

<h2>Orbit</h2>

<form method="GET" action="/index.php">
    <label for="search">Search:</label>
    <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>">
    <input type="submit" value="Submit">
</form>

<?php if(!empty($items)): ?>

    <h3>Search result</h3>

    <?php foreach($items as $item): ?>
        <div class="result">
            <div class="result-url"><?= $item['link'] ?></div>
            <a href="<?= $item['link'] ?>" target="_blank">
                <?= $item['title'] ?>
            </a>
            <div class="result-desc"><?= $item['snippet'] ?? '' ?></div>
        </div>
    <?php endforeach; ?>

<?php elseif(!empty($search)): ?>
    <p>Нічого не знайдено</p>

<?php endif; ?>

</body>
</html>