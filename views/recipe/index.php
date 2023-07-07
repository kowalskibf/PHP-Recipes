Użytkownik: <?php echo $_SESSION['user']->getUsername(); ?> <br/>
<a href="index.php?action=logout">Wylogowanie</a>

<h1>Moje przepisy</h1>

<a href="index.php?action=create">Dodaj przepis</a>

<table>
    <thead>
        <tr>
            <th>Tytuł</th>
            <th>Akcje</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($recipes as $recipe) {?>
        <tr>
            <td>
                <?php
                    echo $recipe->getTitle();
                ?>
            </td>
            <td>
                <a href="index.php?action=view&id=<?php echo $recipe->getId(); ?>">Pokaż</a>
                <a href="index.php?action=edit&id=<?php echo $recipe->getId(); ?>">Edytuj</a>
                <a href="index.php?action=delete&id=<?php echo $recipe->getId(); ?>">Usuń</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<h2>Twoje ulubione przepisy</h2>

<table>
    <thead>
        <tr>
            <th>Tytuł</th>
            <th>Autor</th>
            <th>Akcje</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($favoriteRecipes as $recipe) {?>
        <tr>
            <td>
                <?php echo $recipe->getTitle(); ?>
            </td>
            <td>
                <?php echo $recipe->getUserId(); ?>
            </td>
            <td>
                <a href="index.php?action=viewPublic&id=<?php echo $recipe->getId(); ?>">Pokaż</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<h2>Wszystkie publiczne przepisy</h2>

Filtruj po składnikach: <input type="text" id="filter"></input> <br/><br/>

<?php $i = 0; foreach($allPublicRecipes as $recipe) { ?>
    <div id="div-<?php echo $i; ?>">
        <b>Tytuł:</b> <?php echo $recipe->getTitle(); ?> <br/>
        <b>Autor:</b> <?php echo $recipe->getRecipeUsername(); ?> <br/>
        <b>Składniki:</b>
        <ul>
            <?php foreach($recipe->getIngredients() as $ingredient) { ?>
                <li><?php echo $ingredient->getDescription(); ?></li>
            <?php } ?>
        </ul>
        <a href="index.php?action=viewPublic&id=<?php echo $recipe->getId(); ?>">Wyświetl przepis</a> <br/><br/>
    </div>
<?php $i++; } ?>

<style>
    .hidden{display: none;}
</style>

<script>

    var divs = document.getElementsByTagName("div");
    document.getElementById("filter").addEventListener("input", () => {
        var inputIngredients = document.getElementById("filter").value.toLowerCase().split(",").filter(s => s !== "" && s !== " ").map(s => s.trim());
        for(div of divs)
        {
            if(inputIngredients.length === 0)
            {
                div.classList.remove("hidden");
            }
            else
            {
                var recipeIngredients = div.getElementsByTagName('ul')[0].getElementsByTagName('li');
                var allIncluded = true;
                for(inputIngredient of inputIngredients)
                {
                    var included = false;
                    for(recipeIngredient of recipeIngredients)
                    {
                        if(recipeIngredient.innerHTML.toLowerCase().includes(inputIngredient))
                        {
                            included = true;
                            break;
                        }
                    }
                    if(!included)
                    {
                        allIncluded = false;
                        break;
                    }
                }
                allIncluded ? div.classList.remove("hidden") : div.classList.add("hidden");
            }
        }
    });

</script>