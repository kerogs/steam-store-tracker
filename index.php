<?php require_once './src/php/core.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SST</title>
    <link rel="shortcut icon" href="./src/img/ksmg-light.svg" type="image/x-icon">
    <link rel="stylesheet" href="./src/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- <script src="https://cdn.plot.ly/plotly-latest.min.js"></script> -->

    <!-- <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-financial"></script> -->

</head>

<body>
    <!-- 
    <header>

        <div class="content">
            <img src="./src/img/ksm-logo-white.png" alt="">

            <a href=""><button><i class='bx bxs-dashboard'></i> Dashboard</button></a>
            <a href=""><button><i class='bx bxs-cog'></i> Settings</button></a>
        </div>

    </header> -->

    <div class="container">
        <div class="header">
            <div class="content">
                <img src="./src/img/ksmg-light.svg" alt="">

                <a href=""><button><i class='bx bxs-dashboard'></i> Dashboard</button></a>
                <a href=""><button><i class='bx bxs-cog'></i> Settings</button></a>
                <a href="https://github.com/kerogs/steam-store-tracker" target="_blank"><button><i class='bx bxl-github'></i> Github</button></a>
            </div>
        </div>

        <div class="sidenav">
            <h2>New item</h2>

            <form action="settings-send.php" method="post">
                <input type="text" name="itemName" id="" placeholder="Name" required>
                <input type="url" name="itemURL" id="" placeholder="Url" required>
                <select name="itemQuality" id="">
                    <option value="Factory New">Factory New (0.00 - 0.07)</option>
                    <option value="Minimal Wear">Minimal Wear (0.07 - 0.15)</option>
                    <option value="Field-Tested">Field-Tested (0.15 – 0.38) </option>
                    <option value="Well-Worn">Well-Worn (0.38 – 0.45)</option>
                    <option value="Battle-Scarred">Battle-Scarred (0.45 – 1.00)</option>
                </select>
                <input type="number" name="itemPriceDefault" step="0.01" id="" placeholder="default price" required>
                <input class="greenbtn" type="submit" value="Add">
                <input type="hidden" name="token" value="<?= $_COOKIE['sst_token'] ?>">
            </form>

            <hr>

            <form action="all-refresh.php">
                <input class="bluebtn" type="submit" value="All refresh">
            </form>
        </div>

        <div class="sidecontrol">
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

                $topThreeItems = array_keys(array_slice($itemsWithPercentage, 0, 30));

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


                    echo '<li class="' . $itemTypeColor . '"><span class="' . $itemNameType . '">' . $itemData['itemName'] . '</span> <span class="' . $itemTypeColor . '">' . calculatePercentage($itemData['itemPriceDefault'], $itemData['itemPriceActual']) . ' (' . $itemData['itemPriceActual'] . $itemData['itemType'] . ') ' . $itemTypeIcon . '</span> </li>';
                }

                ?>


            </ul>
        </div>

        <div class="list">
            <table>
                <thead>
                    <th>#</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Quality</th>
                    <th>Price (actual)</th>
                    <th>Price (Default)</th>
                    <th>Item</th>
                    <th>Action</th>
                </thead>
                <tbody>
                    <?php

                    $items = scandir("./data/account/" . $_COOKIE['sst_token'] . "/item");
                    $itemsNumber = 1;
                    foreach ($items as $itemInventory) {
                        if ($itemInventory == '.' || $itemInventory == "..") {
                        } else {
                            // print_r();
                            echo '<tr>';
                            $itemInventoryName = pathinfo($itemInventory);
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
                                    $itemTypeIcon = "<i class='bx bxs-bug' ></i>";
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


                            echo '<td>' . $itemsNumber . '</td>';
                            echo '<td title="/data/account/' . $_COOKIE['sst_token'] . '/item/' . $itemInventory . '">' . $itemInventoryName['filename'] . '</td>';
                            echo '<td class="' . $itemNameType . '"> ' . $itemData['itemName'] . ' </td>';
                            echo '<td> ' . $itemData['itemQuality'] . '</td>';
                            echo '<td class="' . $itemTypeColor . '">' . $itemData['itemPriceActual'] . $itemData['itemType'] . ' (' . calculatePercentage($itemData['itemPriceDefault'], $itemData['itemPriceActual']) . ') ' . $itemTypeIcon . '</td>';
                            echo '<td>' . $itemData['itemPriceDefault'] . $itemData['itemType'] . '</td>';
                            echo '<td><a href="' . $itemData['itemURL'] . '" target="_blank">STORE</a></td>';
                            echo '<td><a href="./action.php?id=' . $itemData['itemID'] . '&delete=true"><button class="red">DELETE</button></a> <a href="./action.php?id=' . $itemData['itemID'] . '&refresh=true"><button class="blue">REFRESH</button></a>  ';
                            echo '</tr>';

                            $itemsNumber++;
                        }
                    }

                    ?>

                </tbody>
            </table>
        </div>

        <div class="control">
            <button data-name="total">Total value</button>
            <button data-name="most-profitable">Most profitable</button>
            <button data-name="ohlc">OHLC</button>
            <button data-name="tracker">Tracker</button>
        </div>

        <div class="stats">
            <div data-object="total">
                <?php require_once './src/php/settings/graphic-circle.php' ?>
            </div>
            <div style="display:none;" data-object="line">
                <?php require_once './src/php/settings/graphic-ohlc.php' ?>
            </div>
            <div style="display:none;" data-object="most-profitable">
                <?php require_once './src/php/settings/graphic-mostprofitable.php' ?>
            </div>
            <div style="display:none;" data-object="tracker">
                <!-- <?php require_once './src/php/settings/graphic-tracker.php' ?> -->
            </div>
        </div>
    </div>


</body>
<!-- script -->
<script>
    // Sélectionner tous les boutons de contrôle
    const buttons = document.querySelectorAll('.control button');

    // Ajouter un gestionnaire d'événements à chaque bouton
    buttons.forEach(button => {
        button.addEventListener('click', () => {
            // Récupérer la valeur de l'attribut data-name du bouton cliqué
            const dataName = button.dataset.name;

            // Masquer tous les éléments de la classe 'stats'
            const stats = document.querySelectorAll('.stats > div');
            stats.forEach(stat => {
                stat.style.display = 'none';
            });

            // Afficher uniquement le div correspondant à l'attribut data-object égal à dataName
            const selectedStat = document.querySelector(`[data-object="${dataName}"]`);
            selectedStat.style.display = 'block';
        });
    });
</script>


</html>