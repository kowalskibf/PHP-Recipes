<h1>Dodaj przepis</h1>

<form action="index.php?action=create" method="post">
    <label for="title">Tytuł:</label>
    <input type="text" name="recipe[title]" id="title">

    <label for="description">Opis:</label>
    <input type="text" name="recipe[description]" id="description">
    
    <p>Składniki:</p>
    <div id="ingredients-container">
        <textarea name="recipe[ingredients][0]" id="ingredient0"></textarea>
    </div>
    <button onclick="addIngredient()">Dodaj składnik</button>
    <button onclick="removeIngredient()">Usuń ostatni składnik</button>
    
    <p>Instrukcje:</p>
    <div id="steps-container">
        <span>Krok 1.</span><textarea name="recipe[steps][0]" id="step0"></textarea>
    </div>
    <button onclick="addStep()">Dodaj krok</button>
    <button onclick="removeStep()">Usuń ostatni krok</button>

    <br/><br/>
    <label for="privacy">Prywatność przepisu:</label>
    <select name="recipe[privacy]" id="privacy">
        <option value=0>Prywatny</option>
        <option value=1>Publiczny</option>
    </select>
    
    <br/><br/>
    <input type="submit" value="Dodaj">
</form>

<script>

    var ingredientsCount = 1;

    function addIngredient()
    {
        event.preventDefault();
        var newIngredient = '<textarea name="recipe[ingredients][' + ingredientsCount.toString() + ']" id="ingredient' + ingredientsCount.toString() + '"></textarea>';
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

    var stepsCount = 1;

    function addStep()
    {
        event.preventDefault();
        var newStepLabel = '<span>Krok ' + (stepsCount + 1).toString() + '.</span>';
        var newStep = '<textarea name="recipe[steps][' + stepsCount.toString() + ']" id="step' + stepsCount.toString() + '"></textarea>';
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