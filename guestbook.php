<?php
// TODO 1: PREPARING ENVIRONMENT
session_start();

$aConfig = require_once 'config.php';
$db = mysqli_connect(
        $aConfig['host'],
        $aConfig['user'],
        $aConfig['pass'],
        $aConfig['name']
);

$comments = [];
$errors = [];
$success = false;

// TODO 3: CODE by REQUEST METHODS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $name  = isset($_POST['name'])  ? trim($_POST['name'])  : '';
    $text  = isset($_POST['text'])  ? trim($_POST['text'])  : '';

    if (empty($email)) $errors[] = 'Email є обов\'язковим полем';
    if (empty($name))  $errors[] = 'Ім\'я є обов\'язковим полем';
    if (empty($text))  $errors[] = 'Текст коментаря є обов\'язковим полем';
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Невірний формат email';
    }

    if (empty($errors)) {
        $name  = mysqli_real_escape_string($db, $name);
        $email = mysqli_real_escape_string($db, $email);
        $text  = mysqli_real_escape_string($db, $text);

        $query = "INSERT INTO comments (name, email, text, date)
                  VALUES ('$name', '$email', '$text', NOW())";
        mysqli_query($db, $query);

        $success = true;
    }
}

// Отримуємо коментарі з БД
$query      = 'SELECT * FROM comments ORDER BY date DESC';
$dbResponse = mysqli_query($db, $query);
$comments   = mysqli_fetch_all($dbResponse, MYSQLI_ASSOC);

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
            GuestBook form
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            Коментар успішно додано!
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <div><?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/guestbook.php">

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    placeholder="your@email.com"
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Ім'я</label>
                            <input
                                    type="text"
                                    class="form-control"
                                    id="name"
                                    name="name"
                                    placeholder="Ваше ім'я"
                                    value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="text" class="form-label">Коментар</label>
                            <textarea
                                    class="form-control"
                                    id="text"
                                    name="text"
                                    rows="4"
                                    placeholder="Ваш відгук..."
                            ><?= htmlspecialchars($_POST['text'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Відправити
                        </button>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <br>

    <div class="card card-primary">
        <div class="card-header bg-body-secondary text-dark">
            Comments
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">

                    <?php if (empty($comments)): ?>
                        <p class="text-muted">Коментарів поки немає. Будьте першим!</p>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title mb-1">
                                        <?= htmlspecialchars($comment['name']) ?>
                                        <small class="text-muted">
                                            &lt;<?= htmlspecialchars($comment['email']) ?>&gt;
                                        </small>
                                    </h6>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($comment['date']) ?>
                                    </small>
                                    <p class="card-text mt-2">
                                        <?= htmlspecialchars($comment['text']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>