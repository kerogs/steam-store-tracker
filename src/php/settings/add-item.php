<h2>NEW ITEM</h2>
<hr>
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
    <input type="submit" value="Add">
    <input type="hidden" name="token" value="<?= $_COOKIE['sst_token'] ?>">
</form>