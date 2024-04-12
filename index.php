<?php require_once './src/php/core.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SST</title>
    <link rel="shortcut icon" href="./src/img/ksmg-light.svg" type="image/x-icon">:
    <link rel="stylesheet" href="./src/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <!-- https://steamcommunity.com/market/listings/730/AK-47%20%7C%20Slate%20%28Minimal%20Wear%29?l=french -->
    <?php

    echo isset($_GET['e']) ? '<div class="notif error"><p>' . $_GET['e'] . '</p></div>' : '';
    echo isset($_GET['n']) ? '<div class="notif notification"><p>' . $_GET['n'] . '</p></div>' : '';
    echo isset($_GET['s']) ? '<div class="notif success"><p>' . $_GET['s'] . '</p></div>' : '';

    ?>

    <div id="settings">
    </div>

    <header>
        <h1>Steam Store Tracker</h1>

        <div class="container">
            <div class="mpi">
                <h2>Most profitable items</h2>
                <ul>
                    <li>Ak74 - Ardoise : <span class="green">+13% (+0.32$) <i class='bx bx-trending-up'></i></span></li>
                    <li>Ak74 - Ardoise : <span class="orange">+0% (0.00$) <i class='bx bx-move-vertical'></i></span></li>
                    <li>Ak74 - Ardoise : <span class="red">-8% (-0.04$) <i class='bx bx-trending-down'></i></span></li>
                </ul>
            </div>
            <div class="graph">
                <h2>Graphic</h2>
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

    <main>

        <table>
            <thead>
                <th>#</th>
                <th>ID</th>
                <th>NAME</th>
                <th>QUALITY</th>
                <th>SELL PREVIEW</th>
                <th>PRICE (ACTUAL)</th>
                <th>PRICE (DEFAULT)</th>
                <th>ITEM LINK</th>
                <th>ACTION</th>
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

                        $priceTwoPercent = round(applyTwoPercent($itemData['itemPriceActual']), 2);
                        switch (true) {
                            case $priceTwoPercent < $itemData['priceDefault']:
                                $itemTypeColorTwoPercent = "red";
                                $itemTypeIconTwoPercent = "<i class='bx bx-trending-down'></i>";
                            case $priceTwoPercent > $itemData['priceDefault']:
                                $itemTypeColorTwoPercent = "green";
                                $itemTypeIconTwoPercent = "<i class='bx bx-trending-up'></i></span>";
                            case $priceTwoPercent == $itemData['priceDefault']:
                                $itemTypeColorTwoPercent = "blue";
                                $itemTypeIconTwoPercent = "<i class='bx bx-move-vertical'></i>";
                            default:
                                $itemTypeColorTwoPercent = "orange";
                                $itemTypeIconTwoPercent = "<i class='bx bxs-bug' ></i>";
                                break;
                        }

                        echo '<td>' . $itemsNumber . '</td>';
                        echo '<td title="/data/account/' . $_COOKIE['sst_token'] . '/item/' . $itemInventory . '">' . $itemInventoryName['filename'] . '</td>';
                        echo '<td> ' . $itemData['itemName'] . ' </td>';
                        echo '<td> ' . $itemData['itemQuality'] . '</td>';
                        echo '<td class="'.$itemTypeColorTwoPercent.'">'. $priceTwoPercent .'$ ('. calculatePercentage($priceTwoPercent, $itemData['itemDefault']) .') '. $itemTypeIconTwoPercent .'</td>';
                        echo '<td class="' . $itemTypeColor . '">' . $itemData['itemPriceActual'] . '$ (' . calculatePercentage($itemData['itemPriceDefault'], $itemData['itemPriceActual']) . ') ' . $itemTypeIcon . '</td>';
                        echo '<td>' . $itemData['itemPriceDefault'] . '$</td>';
                        echo '<td><a href="' . $itemData['itemURL'] . '" target="_blank">STORE</a></td>';
                        echo '<td><button class="red" data-settings="item-delete" data-id="' . $itemInventoryName['filename'] . '">DELETE</button> <button class="blue">REFRESH</button></td>';
                        echo '</tr>';

                        $itemsNumber++;
                    }
                }

                ?>
            </tbody>
        </table>

    </main>

</body>
<script>
    document.addEventListener("click", function(event) {
        const clickedButton = event.target;
        if (clickedButton.tagName === "BUTTON" && clickedButton.dataset.settings) {
            const settingName = clickedButton.dataset.settings;
            const filePath = `./src/php/settings/${settingName}.php`;

            fetch(filePath)
                .then(response => response.text())
                .then(data => {
                    const settingsDiv = document.getElementById("settings");
                    settingsDiv.innerHTML = data;
                    settingsDiv.style.display = "block";
                })
                .catch(error => {
                    console.error("Error fetching settings:", error);
                });
        }
    });
</script>

</html>