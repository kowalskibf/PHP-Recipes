<h1>Moje składniki</h1>

<?php foreach($ingredients as $ingredient) {
    echo $ingredient->getDescription();
    $use = $useCount[spl_object_hash($ingredient)];
    if($use == 0)
    {
        echo '- Składnik niewykorzystywany';
?>
    <form action="index.php?action=myIngredients" method="post">
        <input type="hidden" name="id" value="<?php echo $ingredient->getId(); ?>">
        <button type="submit">Usuń składnik</button>
    </form>
<?php
    }
    else if($use == 1) echo '- Składnik wykorzystywany w 1 przepisie';
    else echo '- Składnik wykorzystywany w '.$use.' przepisach';
    echo '<br/><br/>';
}
?>

<h2>Dodaj składnik</h2>

<form action="index.php?action=myIngredients" method="post">
    <textarea name="ingredient"></textarea>
    <button type="submit">Dodaj składnik</button>
</form>

<a href="index.php?action=myRecipes">Powrót</a>