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
    $password = md5(trim($_POST['password']));

    // Перевірка користувача в БД
    $result = mysqli_query($db, "SELECT * FROM users WHERE email = '$email' AND password = '$password'");

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['auth']  = true;
        $_SESSION['email'] = $user['email'];

        mysqli_close($db);
        header('Location: /admin.php');
        die;
    } else {
        $infoMessage  = "Такого пользователя не существует. Перейдите на страницу регистрации. ";
        $infoMessage .= "<a href='register.php'>Страница регистрации</a>";
    }

} elseif (!empty($_POST)) {
    $infoMessage = 'Заполните форму авторизации!';
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
        <div class="card-header bg-primary text-light">
            Login form
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
                    <input type="submit" class="btn btn-primary" name="form"/>
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