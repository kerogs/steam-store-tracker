<?php
$items = scandir("./data/account/" . $_COOKIE['sst_token'] . "/item");
$itemPercentages = []; // Tableau pour stocker les pourcentages de chaque élément

foreach ($items as $itemInventory) {
    if ($itemInventory != '.' && $itemInventory != "..") {
        $itemJSON = file_get_contents("./data/account/" . $_COOKIE['sst_token'] . "/item/$itemInventory");
        $itemData = json_decode($itemJSON, true);

        // Assurez-vous que l'élément existe dans les données et que le prix par défaut n'est pas 0
        if (isset($itemData['itemPriceActual']) && isset($itemData['itemPriceDefault']) && $itemData['itemPriceDefault'] != 0) {
            // Calculer le pourcentage de changement de prix
            if ($itemData['itemPriceActual'] != $itemData['itemPriceDefault']) {
                $percentage = (($itemData['itemPriceActual'] - $itemData['itemPriceDefault']) / $itemData['itemPriceDefault']) * 100;
            } else {
                $percentage = 0; // Si le prix est resté le même, attribuer 0% de changement
            }
            $itemPercentages[$itemData['itemName']] = $percentage;
        }
    }
}

// Convertir les données en format compatible avec Chart.js
$labels = [];
$data = [];

foreach ($itemPercentages as $itemName => $percentage) {
    $labels[] = $itemName;
    $data[] = $percentage;
}

?>

<canvas id="myBarChart"></canvas>

<script>
    // Récupérer les données PHP
    var labels = <?php echo json_encode($labels); ?>;
    var data = <?php echo json_encode($data); ?>;
    var colors = <?php echo json_encode($colors); ?>;
    var borderColors = <?php echo json_encode($borderColors); ?>;

    // Créer le graphique à barres
    var ctx = document.getElementById('myBarChart').getContext('2d');
    var myBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Prices',
                data: data,
                backgroundColor: colors,
                borderColor: borderColors,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.2)' // Couleur des grilles de fond pour l'axe Y
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.2)' // Couleur des grilles de fond pour l'axe X
                    }
                }
            }
        }
    });
</script>