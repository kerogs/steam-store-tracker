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

    <?php include_once './src/php/header.php' ?>

    <main>

        <table>
            <thead>
                <th>#</th>
                <th>ID</th>
                <th>NAME</th>
                <th>QUALITY</th>
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
                        echo '<td><button class="red" data-settings="item-delete" data-reload="true" data-id="' . $itemInventoryName['filename'] . '">DELETE</button> <button data-settings="item-refresh" data-id="'.$itemData['itemID'].'" class="blue">REFRESH</button></td>';
                        echo '</tr>';

                        $itemsNumber++;
                    }
                }

                ?>
            </tbody>
        </table>

    </main>

</body>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.min.js"></script>
<script src="./src/js/script.js"></script> -->
<script>
document.addEventListener("click", function(event) {
    const clickedButton = event.target;
    if (clickedButton.tagName === "BUTTON" && clickedButton.dataset.settings) {
        const settingName = clickedButton.dataset.settings;
        const id = clickedButton.dataset.id || "";
        const reload = clickedButton.dataset.reload === "true";
        const filePath = `./src/php/settings/${settingName}.php?id=${id}`;

        fetch(filePath)
            .then(response => response.text())
            .then(data => {
                const settingsDiv = document.getElementById("settings");
                settingsDiv.innerHTML = data;
                settingsDiv.style.display = "block";

                if (reload) {
                    setTimeout(() => {
                        location.reload();
                    }, 100); // Delay rechargement de la page pour laisser le temps pour l'affichage des données
                }
            })
            .catch(error => {
                console.error("Error fetching settings:", error);
            });
    }
});


</script>

</html>