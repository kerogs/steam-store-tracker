<?php

echo $path;
if (!isset($_COOKIE['sst_token'])) {
    header('Location: ./?e=Error, user without token');
    exit;
}

require_once './src/php/core.php';



$path = './data/account/' . $_COOKIE['sst_token'];

if (!is_dir("$path/item/")) {
    mkdir("$path/item");
}

if (isset($_POST['itemName'])) {
    $itemID = uniqid();

    // ? get real item name
    $segments = explode('/', rtrim($_POST['itemURL'], '/'));
    $itemURLName = urldecode(end($segments));
    $itemURLName = urlencode($itemURLName);

    // ? get information
    $itemURLJSON = "https://steamcommunity.com/market/priceoverview/?country=US&currency=3&appid=730&market_hash_name=$itemURLName";
    $get = file_get_contents($itemURLJSON);
    $getJSON = json_decode($get);
    $itemPriceActual = str_replace(',', '.', preg_replace('/[^0-9,.]/', '', $getJSON->lowest_price));
    $currency = preg_replace('/[0-9,.]/', '', $getJSON->lowest_price);

    $data = [
        "itemID" => $itemID,
        "itemName" => $_POST['itemName'],
        "itemURL" => $_POST['itemURL'],
        "itemURLJSON" => $itemURLJSON,
        "itemQuality" => $_POST['itemQuality'],
        "itemPriceDefault" => $_POST['itemPriceDefault'],
        "itemPriceActual" => $itemPriceActual,
        "itemType" => $currency
    ];

    

    $jsonData = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents("$path/item/$itemID.json", $jsonData);

    $returnStatus = "New item : <b>" . $_POST['itemName'] . "</b>";
}

if (isset($returnStatus)) {
    header("Location: ./?s=$returnStatus");
} else {
    header("Location: ./?n=no value return.");
}