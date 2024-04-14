<?php
// Chemin du dossier contenant les fichiers JSON des éléments
$itemDirectory = './data/account/' . $_COOKIE['sst_token'] . "/item/";

// Liste tous les fichiers JSON dans le dossier
$itemFiles = glob($itemDirectory . "*.json");

// Parcourir chaque fichier JSON pour les rafraîchir
foreach ($itemFiles as $itemFile) {
    // Charger le contenu JSON depuis le fichier
    $itemJSON = json_decode(file_get_contents($itemFile), true);

    // Vérifier si le décodage JSON a réussi
    if ($itemJSON !== null) {
        // Récupérer l'URL JSON de l'élément
        $itemURLJSON = $itemJSON['itemURLJSON'];

        // Récupérer les données JSON à partir de l'URL
        $getJSON = file_get_contents($itemURLJSON);
        $decodedJSON = json_decode($getJSON);

        // Extraire le prix le plus bas de l'objet JSON
        $itemPriceActual = str_replace(',', '.', preg_replace('/[^0-9,.]/', '', $decodedJSON->lowest_price));

        // Mettre à jour le prix actuel de l'élément
        $itemJSON['itemPriceActual'] = $itemPriceActual;

        // Ajouter un suivi avec la date actuelle et le nouveau prix
        $newTrack = [
            'date' => date("d/m/Y"),
            'price' => $itemPriceActual
        ];

        // Ajouter le suivi à la liste de suivi
        if (!isset($itemJSON['track'])) {
            $itemJSON['track'] = []; // Initialiser la liste de suivi si elle n'existe pas déjà
        }
        $itemJSON['track'][] = $newTrack;

        // Enregistrer le contenu JSON mis à jour dans le fichier
        file_put_contents($itemFile, json_encode($itemJSON, JSON_PRETTY_PRINT));

        // Afficher le message de mise à jour pour cet élément
        echo "Update for: " . $itemJSON['itemName'] . "<br>";
    }
}
?>
