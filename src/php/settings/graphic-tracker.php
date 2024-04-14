<?php
// Collecte des données en PHP (à remplacer par votre propre logique de collecte de données)
$labels = [];

// Boucle pour obtenir les étiquettes des 12 derniers jours
for ($i = 11; $i >= 0; $i--) {
    // Date actuelle moins $i jours
    $date = date('d/m/Y', strtotime("-$i days"));
    // Ajouter la date au tableau des étiquettes
    $labels[] = $date;
}

$items = scandir("./data/account/" . $_COOKIE['sst_token'] . "/item");
$itemDatasets = []; // Tableau pour stocker les ensembles de données de chaque élément

foreach ($items as $itemInventory) {
    if ($itemInventory != '.' && $itemInventory != "..") {
        $itemJSON = file_get_contents("./data/account/" . $_COOKIE['sst_token'] . "/item/$itemInventory");
        $itemData = json_decode($itemJSON, true);

        // Assurez-vous que l'élément existe dans les données avant de l'ajouter au tableau des ensembles de données
        if (isset($itemData['track'])) {
            // Collecter les prix pour chaque élément
            $prices = [];
            foreach ($itemData['track'] as $track) {
                $prices[] = $track['price'];
            }
            // Créer l'ensemble de données pour l'élément
            $itemDatasets[$itemData['itemName']] = ['tracker' => ['price' => $prices]];
        }
    }
}

// Données pour les datasets
$dataSets = [];
foreach ($itemDatasets as $itemName => $data) {
    $dataSets[] = [
        'label' => $itemName,
        'data' => $data['tracker']['price'],
        'fill' => false,
        'borderColor' => 'rgb(75, 192, 192)',
        'tension' => 0.1 // Courbe plus lisse
    ];
}
?>

<canvas id="myLineChart"></canvas>
<script>
    // Données pour le graphique
    var data = {
        labels: <?php echo json_encode($labels); ?>,
        datasets: <?php echo json_encode($dataSets); ?>
    };

    // Options du graphique
    var options = {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    };

    // Création du graphique
    var ctx = document.getElementById('myLineChart').getContext('2d');
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: data,
        options: options
    });
</script>
