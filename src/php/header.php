<header>
        <h1>Steam Store Tracker</h1>

        <div class="container">
            <div class="mpi">
                <h2>Most profitable items</h2>
                <ul>
                    <?php

                    $items = scandir("./data/account/" . $_COOKIE['sst_token'] . "/item");
                    $itemsWithPercentage = [];

                    foreach ($items as $itemInventory) {
                        if ($itemInventory == '.' || $itemInventory == "..") {
                            continue;
                        }

                        $itemJSON = file_get_contents("./data/account/" . $_COOKIE['sst_token'] . "/item/$itemInventory");
                        $itemData = json_decode($itemJSON, true);

                        $percentage = calculatePercentage($itemData['itemPriceActual'], $itemData['itemPriceDefault']);
                        if ($percentage != 0) {
                            $itemsWithPercentage[$itemInventory] = $percentage;
                        }
                    }

                    // Tri du tableau en fonction des pourcentages (du plus grand au plus petit)
                    uasort($itemsWithPercentage, function ($a, $b) {

                        return $b <=> $a;
                    });

                    $topThreeItems = array_keys(array_slice($itemsWithPercentage, 0, 4));

                    foreach ($topThreeItems as $itemInventory) {
                        $itemJSON = file_get_contents("./data/account/" . $_COOKIE['sst_token'] . "/item/$itemInventory");
                        $itemData = json_decode($itemJSON, true);

                        switch (true) {
                            case $itemData['itemPriceDefault'] < $itemData['itemPriceActual']:
                                $itemTypeColor = "green";
                                $itemTypeIcon = "<i class='bx bx-trending-up'></i></span>";
                                break;
                            case $itemData['itemPriceDefault'] > $itemData['itemPriceActual']:
                                $itemTypeColor = "red";
                                $itemTypeIcon = "<i class='bx bx-trending-down'></i>";
                                break;
                            case $itemData['itemPriceDefault'] == $itemData['itemPriceActual']:
                                $itemTypeColor = "blue";
                                $itemTypeIcon = "<i class='bx bx-move-vertical'></i>";
                                break;
                            default:
                                $itemTypeColor = "orange";
                                $itemTypeIcon = "<i class='bx bxs-bug'></i>";
                                break;
                        }

                        switch (true) {
                            case stripos(strtolower($itemData['itemName']), "souvenir") !== false:
                                $itemNameType = "souvenir";
                                break;
                            case stripos(strtolower($itemData['itemName']), "stattrak") !== false:
                                $itemNameType = "stattrak";
                                break;
                            default:
                                $itemNameType = "blue";
                                break;
                        }


                        echo '<li>[ <span class="' . $itemNameType . '">' . $itemData['itemName'] . '</span> ] <span class="' . $itemTypeColor . '">' . calculatePercentage($itemData['itemPriceDefault'], $itemData['itemPriceActual']) . ' (' . $itemData['itemPriceActual'] . $itemData['itemType'] . ') ' . $itemTypeIcon . '</span> </li>';
                    }

                    ?>


                </ul>
            </div>
            <div class="graph">
                <h2>Based on your items with the highest current price</h2>
                <canvas id="myPieChart" width="200" height="200"></canvas>

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

            </div>
            <div>
                <h2>Settings</h2>
                ID : <?= $_COOKIE['sst_token'] ?> <br>
                Version : <span class="green"><?= $serverVersion ?></span>
                <div class="actionlist">
                    <button data-settings="add-item">New item <i class='bx bxs-message-alt-add'></i></button>
                </div>
            </div>
        </div>
    </header>