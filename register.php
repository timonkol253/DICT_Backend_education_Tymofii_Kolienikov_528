<?php
session_start();

if (!empty($_SESSION['auth'])) {
    header('Location: /admin.php');
    die;
}

$aConfig = require_once 'config.php';
$db = mysqli_connect(
        $aConfig['host'],
        $aConfig['user'],
        $aConfig['pass'],
        $aConfig['name']
);

$infoMessage = '';

if (!empty($_POST['email']) && !empty($_POST['password'])) {

    $email    = mysqli_real_escape_string($db, trim($_POST['email']));
    $password = mysqli_real_escape_string($db, trim($_POST['password']));

    // Перевірка чи вже існує користувач
    $result = mysqli_query($db, "SELECT id FROM users WHERE email = '$email'");

    if (mysqli_num_rows($result) > 0) {
        $infoMessage  = "Такой пользователь уже существует! Перейдите на страницу входа. ";
        $infoMessage .= "<a href='/login.php'>Страница входа</a>";
    } else {
        // Створення нового користувача
        $hashedPassword = md5($password);
        mysqli_query($db, "INSERT INTO users (email, password) VALUES ('$email', '$hashedPassword')");

        mysqli_close($db);
        header('Location: /login.php');
        die;
    }

} elseif (!empty($_POST)) {
    $infoMessage = 'Заполните форму регистрации!';
}

mysqli_close($db);
?>
<!DOCTYPE html>
<html>
<?php require_once 'sectionHead.php' ?>
<body>
<div class="container">

    <?php require_once 'sectionNavbar.php' ?>

    <br>

    <div class="card card-primary">
        <div class="card-header bg-success text-light">
            Register form
        </div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" type="email" name="email"/>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input class="form-control" type="password" name="password"/>
                </div>
                <br>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" name="formRegister"/>
                </div>
            </form>

            <?php if ($infoMessage): ?>
                <hr/>
                <span style="color:red"><?= $infoMessage ?></span>
            <?php endif; ?>

        </div>
    </div>
</div>
</body>
</html>