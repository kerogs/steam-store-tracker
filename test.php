<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test</title>
</head>
<body>

<?php

    $get = file_get_contents("https://steamcommunity.com/market/priceoverview/?country=US&currency=1&appid=730&market_hash_name=Glock-18%20|%20Candy%20Apple%20(Minimal%20Wear)");
    $getJSON = json_decode($get);
    print_r($getJSON);
?>

    <hr>

    <?= $getJSON->lowest_price ?>

    <hr>

</body>
</html>