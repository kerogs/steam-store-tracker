<?php

echo $path;
if (!isset($_COOKIE['sst_token'])) {
    header('Location: ./?e=Error, user without token');
    exit;
}

require_once './src/php/core.php';
$path = './data/account/' . $_COOKIE['sst_token'];

if ($_GET['refresh']) {
    // Chemin du fichier JSON
    $pathJSON = './data/account/' . $_COOKIE['sst_token'] . "/item/" . $_GET['id'] . ".json";

    // Charger le contenu JSON depuis le fichier
    $itemJSON = json_decode(file_get_contents($pathJSON), true);

    // Vérifier si le décodage JSON a réussi
    if ($itemJSON !== null) {
        // Récupérer la date actuelle au format jj/mm/aaaa
        $date = date("d/m/Y");

        // Récupérer le prix de l'élément
        $price = $itemJSON['itemPriceActual'];

        $i = 0;

        // Créer un nouvel élément de suivi
        $newTrack = [
            $i = [
                'date' => $date,
                'price' => $price
            ]
        ];

        // Ajouter le nouvel élément de suivi à la liste de suivi
        if (!isset($itemJSON['track'])) {
            $itemJSON['track'] = []; // Initialiser la liste de suivi si elle n'existe pas déjà
        }
        $itemJSON['track'][] = $newTrack;

        $itemURLJSON = $itemJSON['itemURLJSON'];
        $get = file_get_contents($itemURLJSON);
        $getJSON = json_decode($get);
        $itemPriceActual = str_replace(',', '.', preg_replace('/[^0-9,.]/', '', $getJSON->lowest_price));

        $itemJSON['itemPriceActual'] = $itemPriceActual;

        // Enregistrer le contenu JSON mis à jour dans le fichier
        file_put_contents($pathJSON, json_encode($itemJSON, JSON_PRETTY_PRINT));
    }

    $returnStatus = "Update for :".$itemJSON['itemName'];
}

if (isset($returnStatus)) {
    header("Location: ./?s=$returnStatus");
} else {
    header("Location: ./?n=no value return.");
}
