<h1>Szczegóły przepisu</h1>

<h2>Nazwa: <?php echo $recipe->getTitle(); ?></h2>
<h3>Opis: <?php echo $recipe->getDescription(); ?></h3>
<p>Składniki:</p>
<?php
    foreach($recipe->getIngredients() as $ingredient)
    {
        echo $ingredient->getDescription().'<br/>';
    }
?>
<p>Instrukcje:</p>
<?php
    foreach($recipe->getSteps() as $step)
    {
        echo $step->getNumber().': '.$step->getDescription().'<br/>';
    }
?>
<br/>
<?php echo $recipe->getIsPublic() ? 'Publiczny' : 'Prywatny'; ?>
<br/><br/>
<a href="index.php">Powrót</a>
