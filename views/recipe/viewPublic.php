<h1>Szczegóły przepisu</h1>

<?php if($favorite) { ?>
    Przepis znajduje się w Twoich ulubionych!
    <form action="index.php?action=viewPublic&id=<?php echo $_GET['id']; ?>&removeFromFav=1" method="post">
        <input type="submit" value="Usuń z ulubionych">
    </form>
<?php } else { ?>
    <form action="index.php?action=viewPublic&id=<?php echo $_GET['id']; ?>&addToFav=1" method="post">
        <input type="submit" value="Dodaj do ulubionych">
    </form>
<?php } ?>

Autor: <?php echo $recipe->getUserId(); ?>

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
<a href="index.php">Powrót</a>
