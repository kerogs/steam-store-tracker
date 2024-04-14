<canvas id="myPieChart"></canvas>

<?php
$items = scandir("./data/account/" . $_COOKIE['sst_token'] . "/item");
$itemPrices = []; // Tableau pour stocker les prix de chaque élément

foreach ($items as $itemInventory) {
    if ($itemInventory != '.' && $itemInventory != "..") {
        $itemJSON = file_get_contents("./data/account/" . $_COOKIE['sst_token'] . "/item/$itemInventory");
        $itemData = json_decode($itemJSON, true);

        // Assurez-vous que l'élément existe dans les données avant de l'ajouter au tableau des prix
        if (isset($itemData['itemPriceActual'])) {
            $itemPrices[$itemData['itemName']] = $itemData['itemPriceActual'];
        }
    }
}

// Convertir les données en format compatible avec Chart.js
$labels = [];
$data = [];

foreach ($itemPrices as $itemName => $price) {
    $labels[] = $itemName;
    $data[] = $price;
}


$colors = [
    'rgba(255, 99, 132, 0.5)',   // Rouge
    'rgba(54, 162, 235, 0.5)',   // Bleu
    'rgba(255, 206, 86, 0.5)',   // Jaune
    'rgba(75, 192, 192, 0.5)',   // Turquoise
    'rgba(153, 102, 255, 0.5)',  // Violet
    'rgba(255, 159, 64, 0.5)',   // Orange
    'rgba(0, 204, 102, 0.5)',    // Vert clair
    'rgba(255, 204, 0, 0.5)',    // Or
    'rgba(204, 102, 255, 0.5)',  // Pourpre
    'rgba(0, 153, 153, 0.5)',    // Vert foncé
    'rgba(102, 204, 255, 0.5)',  // Bleu clair
    'rgba(255, 102, 204, 0.5)',  // Rose
    'rgba(255, 153, 0, 0.5)',    // Orange vif
    'rgba(102, 255, 102, 0.5)'   // Vert vif
    // Ajoutez plus de couleurs si nécessaire
];

$borderColors = [
    'rgba(255, 99, 132, 1)',
    'rgba(54, 162, 235, 1)',
    'rgba(255, 206, 86, 1)',
    'rgba(75, 192, 192, 1)',
    'rgba(153, 102, 255, 1)',
    'rgba(255, 159, 64, 1)',
    'rgba(0, 204, 102, 1)',
    'rgba(255, 204, 0, 1)',
    'rgba(204, 102, 255, 1)',
    'rgba(0, 153, 153, 1)',
    'rgba(102, 204, 255, 1)',
    'rgba(255, 102, 204, 1)',
    'rgba(255, 153, 0, 1)',
    'rgba(102, 255, 102, 1)'
];
?>

<script>
    var ctx = document.getElementById('myPieChart').getContext('2d');
    var myPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Price',
                data: <?php echo json_encode($data); ?>,
                backgroundColor: <?php echo json_encode($colors); ?>,
                borderColor: <?php echo json_encode($borderColors); ?>,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
            }
        }
    });
</script>