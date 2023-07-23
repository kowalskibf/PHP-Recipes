<h1>Edytuj przepis</h1>

<form action="index.php?action=edit&id=<?php echo $recipe->getId(); ?>" method="post">
    <label for="title">Tytuł:</label>
    <input type="text" name="recipe[title]" id="title" value="<?php echo $recipe->getTitle(); ?>" required>

    <label for="description">Opis:</label>
    <input type="text" name="recipe[description]" id="description" value="<?php echo $recipe->getDescription(); ?>" required>
    
    <p>Składniki:</p>
    <div id="ingredients-container">
        <?php $i = 0; foreach($recipe->getIngredients() as $ingredient) { ?>
            <textarea name="recipe[ingredients][<?php echo $i; ?>]" id="ingredient<?php echo $i; ?>" required><?php echo $ingredient->getDescription(); ?></textarea>
        <?php $i++; } ?>
    </div>
    <button onclick="addIngredient()">Dodaj składnik</button>
    <button onclick="removeIngredient()">Usuń ostatni składnik</button>
    
    <p>Instrukcje:</p>
    <div id="steps-container">
        <?php $i = 0; foreach($recipe->getSteps() as $step) { ?>
            <span>Krok <?php echo $i+1; ?>.</span><textarea name="recipe[steps][<?php echo $i; ?>]" id="step<?php echo $i; ?>" required><?php echo $step->getDescription(); ?></textarea>
        <?php $i++ ;} ?>
    </div>
    <button onclick="addStep()">Dodaj krok</button>
    <button onclick="removeStep()">Usuń ostatni krok</button>
    
    <br/><br/>
    <label for="privacy">Prywatność przepisu:</label>
    <select name="recipe[privacy]" id="privacy">
        <option value=0>Prywatny</option>
        <option value=1<?php echo $recipe->getIsPublic() ? ' selected="selected"' : ''; ?>>Publiczny</option>
    </select>

    <br/><br/>
    <input type="submit" value="Save">
</form>

<script>

    var ingredientsCount = <?php echo count($recipe->getIngredients()); ?>;

    function addIngredient()
    {
        event.preventDefault();
        var newIngredient = '<textarea name="recipe[ingredients][' + ingredientsCount.toString() + ']" id="ingredient' + ingredientsCount.toString() + '" required></textarea>';
        document.getElementById('ingredients-container').innerHTML += newIngredient;
        ingredientsCount++;
    }

    function removeIngredient()
    {
        event.preventDefault();
        if(ingredientsCount > 1)
        {
            document.getElementById('ingredient' + (ingredientsCount - 1).toString()).remove();
            ingredientsCount--;
        }
    }

    var stepsCount = <?php echo count($recipe->getSteps()); ?>;

    function addStep()
    {
        event.preventDefault();
        var newStepLabel = '<span>Krok ' + (stepsCount + 1).toString() + '.</span>';
        var newStep = '<textarea name="recipe[steps][' + stepsCount.toString() + ']" id="step' + stepsCount.toString() + '" required></textarea>';
        document.getElementById('steps-container').innerHTML += newStepLabel
        document.getElementById('steps-container').innerHTML += newStep;
        stepsCount++;
    }

    function removeStep()
    {
        event.preventDefault();
        if(stepsCount > 1)
        {
            var e = document.getElementById('steps-container');
            document.getElementById('step' + (stepsCount - 1).toString()).remove();
            e.removeChild(e.lastChild);
            stepsCount--;
        }
    }

</script>