<?php

echo $path;
if (!isset($_COOKIE['sst_token'])) {
    header('Location: ./?e=Error, user without token');
    exit;
}

require_once './src/php/core.php';

$path = './data/account/'.$_COOKIE['sst_token'];

if(!is_dir("$path/item/")){
    mkdir("$path/item");
}

if (isset($_POST['itemName'])) {
    $itemID = uniqid();
    $data = [
        "itemID" => $itemID,
        "itemName" => $_POST['itemName'],
        "itemURL" => $_POST['itemURL'],
        "itemQuality" => $_POST['itemQuality'],
        "itemPriceDefault" => $_POST['itemPriceDefault'],
        "itemPriceActual" => getItem($_POST['itemURL'])["prix"],
    ];

    $jsonData = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents("$path/item/$itemID.json", $jsonData);

    $returnStatus = "New item : <b>".$_POST['itemName']."</b>";
}

if(isset($returnStatus)){
    header("Location: ./?s=$returnStatus");
} else{
    header("Location: ./?n=no value return.");
}