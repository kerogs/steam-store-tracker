<?php
// ! Remove this 2 lines if you want to show error
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 'Off');


function tokenMaker($longueur = 18)
{
    $caracteres_autorises = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_()';
    $nb_caracteres = strlen($caracteres_autorises);
    $token = '';

    for ($i = 0; $i < $longueur; $i++) {
        $indice = rand(0, $nb_caracteres - 1);
        $token .= $caracteres_autorises[$indice];
    }

    return $token;
}

$today_date = date("Y m d");
if (!isset($_COOKIE['sst_token'])) {

    $date_expiration = time() + (10 * 365 * 24 * 60 * 60);
    $token = uniqid() . '-' . tokenMaker();
    setcookie('sst_token', $token, $date_expiration);
    mkdir('./data/account/' . $token);

    $data = array(
        "token" => $token,
        "currency" => 3,
        "country" => "US",
        "appid" => "730",
        "theme" => array(
            "light" => true
        )
    );
    $json_data = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents("./data/account/$token/data.json", $json_data);

    header('Location: ./');
    exit;
} else {
    $date_expiration = time() + (10 * 365 * 24 * 60 * 60);
    setcookie('token', uniqid() . '_' . $_COOKIE['sst_token'], $date_expiration);
}

$jsonServData = file_get_contents('./data/serverData.json');
$servData = json_decode($jsonServData, true);

// ? Path to the account
$tokenPath = "./data/account/" . $_COOKIE['sst_token'];
$userJSON = json_decode($tokenPath."/data.json", true);

// ? check for import
// Chemin vers le fichier JSON de l'utilisateur
$userJSONPath = "./data/account/" . $_COOKIE['sst_token'] . "/data.json";

// Vérifiez si le fichier JSON de l'utilisateur existe avant de le charger
if (file_exists($userJSONPath)) {
    $userJSONContent = file_get_contents($userJSONPath);
    $userJSON = json_decode($userJSONContent, true);

    if (isset($userJSON['tokenTarget'])) {
        $tokenTargetPath = "./data/account/" . $userJSON['tokenTarget'];
        foreach (scandir("$tokenTargetPath/item/") as $tokenTargetItem) {
            copy("$tokenTargetPath/item/$tokenTargetItem", "$tokenPath/item/$tokenTargetItem");

            $existingJSON = file_get_contents("$tokenPath/item/$tokenTargetItem");
            $existingData = json_decode($existingJSON, true);

            $existingData['tokenTarget'] = $_POST['tokenTarget'];

            file_put_contents("$tokenPath/item/$tokenTargetItem", json_encode($existingData, JSON_PRETTY_PRINT));
        }
    }
}

// ? serverDATA
$serverJSON = file_get_contents('./data/serverData.json');
$serverDATA = json_decode($serverJSON, true);
$serverVersion = $serverDATA['version'];




















// ? functions
function getItem($url)
{
    $doc = new DOMDocument();

    libxml_use_internal_errors(true);

    $doc->loadHTMLFile($url);

    $xpath = new DOMXPath($doc);

    $elements = $xpath->query("//div[contains(@class, 'market_listing_row market_recent_listing_row')]");

    if ($elements->length > 0) {
        $element = $elements->item(0);

        $prixElement = $xpath->query(".//span[contains(@class, 'market_listing_price market_listing_price_with_fee')]", $element)->item(0);

        if ($prixElement) {

            $prix = trim($prixElement->nodeValue);


            $prix = preg_replace("/[^0-9.]/", "", $prix);
        } else {
            $prix = false;
        }
    } else {
        $prix = false;
    }

    return ['prix' => $prix];
}




function calculatePercentage($entry_price, $current_price) {
    // Calculating the percentage
    $percentage = (($current_price - $entry_price) / $entry_price) * 100;

    // Formatting the percentage
    $formatted_percentage = number_format(abs($percentage), 2) . "%";

    // Adding the appropriate sign
    if ($percentage > 0) {
        $formatted_percentage = "+" . $formatted_percentage;
    } elseif ($percentage == 0) {
        $formatted_percentage = "+0.00%";
    } else {
        $formatted_percentage = "-" . $formatted_percentage;
    }

    return $formatted_percentage;
}

function applyTwoPercent($number) {
    $reduction = $number * 0.10;
    $new_number = $number - $reduction;
    return $new_number;
};

