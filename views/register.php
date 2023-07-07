<!DOCTYPE html>
<html>
<head>
    <title>Rejestracja</title>
</head>
<body>
    <h1>Rejestracja</h1>
    <form action="index.php?action=register" method="POST">
        <input type="hidden" name="action" value="register">
        <label for="username">Nazwa użytkownika:</label>
        <input type="text" name="username" required><br>
        <label for="password">Hasło:</label>
        <input type="password" name="password" required><br>
        <input type="submit" value="Zarejestruj">
    </form>
    <a href="index.php?action=login">Logowanie</a>
</body>
</html>
