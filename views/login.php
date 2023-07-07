<!DOCTYPE html>
<html>
<head>
    <title>Logowanie</title>
</head>
<body>
    <h1>Logowanie</h1>
    <form action="index.php?action=login" method="POST">
        <input type="hidden" name="action" value="login">
        <label for="username">Nazwa użytkownika:</label>
        <input type="text" name="username" required><br>
        <label for="password">Hasło:</label>
        <input type="password" name="password" required><br>
        <input type="submit" value="Zaloguj">
    </form>
    <a href="index.php?action=register">Rejestracja</a>
</body>
</html>

