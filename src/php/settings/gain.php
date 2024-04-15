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

                $topThreeItems = array_keys(array_slice($itemsWithPercentage, 0, 100));

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

                    $gain = $itemData['itemPriceActual'] - $itemData['itemPriceDefault']; 
                
                    // echo '<li title="'.$itemData['itemID'].'" class="' . $itemTypeColor . '"><span class="' . $itemNameType . '">' . $itemData['itemName'] . '</span> <span class="' . $itemTypeColor . '">' . calculatePercentage($itemData['itemPriceDefault'], $itemData['itemPriceActual']) . ' (' . $itemData['itemPriceActual'] . $itemData['itemType'] . ') ' . $itemTypeIcon . '</span> </li>';
                    echo '<li title="'.$itemData['itemID'].'" class="' . $itemTypeColor . '"><span class="' . $itemNameType . '">' . $itemData['itemName'] . '</span> => '. $gain . $itemData['itemType'] . $itemTypeIcon . '</span> </li>';
                }

                ?>