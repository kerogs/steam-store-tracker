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


if (isset($_POST['tokenTarget'])) {
    $tokenTargetPath = './data/account/' . $_POST['tokenTarget'];

    if (!is_dir("$path/item/")) mkdir("$path/item/");

    $i=0;

    if ($_POST['tokenTargetAction'] == 1) {
        foreach (scandir("$tokenTargetPath/item/") as $tokenTargetItem) {
            copy("$tokenTargetPath/item/$tokenTargetItem", "$path/item/$tokenTargetItem");

            $existingJSON = file_get_contents("$path/item/$tokenTargetItem");
            $existingData = json_decode($existingJSON, true);
            $existingData['tokenTarget'] = $_POST['tokenTarget'];

            file_put_contents("$path/item/$tokenTargetItem", json_encode($existingData, JSON_PRETTY_PRINT));
            $i++;
        }

        $returnStatus = "Copy all <b>OK</b> from ".$_POST['tokenTarget']."(i=$i)";
    }

    if ($_POST['tokenTargetAction'] == 2) {

        $i = 0;

        // add target
        $jsonUser = file_get_contents("$path/data.json");
        $jsonUserJSON = json_decode($jsonUser, true);
        $jsonUserJSON['tokenTarget'] = $_POST['tokenTarget'];
        file_put_contents("$path/data.json", json_encode($jsonUserJSON, JSON_PRETTY_PRINT));

        // first add
        foreach (scandir("$tokenTargetPath/item/") as $tokenTargetItem) {
            copy("$tokenTargetPath/item/$tokenTargetItem", "$path/item/$tokenTargetItem");

            $existingJSON = file_get_contents("$path/item/$tokenTargetItem");
            $existingData = json_decode($existingJSON, true);
            $existingData['tokenTarget'] = $_POST['tokenTarget'];

            file_put_contents("$path/item/$tokenTargetItem", json_encode($existingData, JSON_PRETTY_PRINT));
            $i++;
        }

        $returnStatus = "Copy all <b>OK</b> + Add in data.json from ".$_POST['tokenTarget']."(i=$i)";
    }

    if($_POST['tokenTargetAction'] == 3){

        // remove tokenTarget
        $jsonUser = file_get_contents("$path/data.json");
        $jsonUserJSON = json_decode($jsonUser, true);
        $jsonUserJSON['tokenTarget'] = false;
        file_put_contents("$path/data.json", json_encode($jsonUserJSON, JSON_PRETTY_PRINT));


        $i = 0;

        foreach(scandir("$path/item/") as $itemToUnlink){
            $itemToUnlinkJSON = json_decode(file_get_contents("$path/item/$itemToUnlink"), true);

            if($itemToUnlinkJSON['tokenTarget'] == $_POST['tokenTarget']){
                unlink("$path/item/$itemToUnlink");
                $i++;
            }
        }

        $returnStatus = "Removed all from ".$_POST['tokenTarget']."(i=$i)";
    }
}

if (isset($returnStatus)) {
    header("Location: ./?s=$returnStatus");
} else {
    header("Location: ./?n=no value return.");
}