<?php
// TODO 1: PREPARING ENVIRONMENT
session_start();

$comments = [];
$errors = [];
$success = false;
$commentsFile = 'comments.csv';

// Функція читання коментарів з файлу
function getComments($filename) {
    $comments = [];
    if (file_exists($filename)) {
        $fileStream = fopen($filename, "r");
        while (!feof($fileStream)) {
            $jsonString = fgets($fileStream);
            $array = json_decode($jsonString, true);
            if (empty($array)) break;
            $comments[] = $array;
        }
        fclose($fileStream);
    }
    return $comments;
}

// TODO 2: ROUTING — нічого не потрібно, одна сторінка

// TODO 3: CODE by REQUEST METHODS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Перевірка наявності даних
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $name  = isset($_POST['name'])  ? trim($_POST['name'])  : '';
    $text  = isset($_POST['text'])  ? trim($_POST['text'])  : '';

    // Валідація
    if (empty($email)) $errors[] = 'Email є обов\'язковим полем';
    if (empty($name))  $errors[] = 'Ім\'я є обов\'язковим полем';
    if (empty($text))  $errors[] = 'Текст коментаря є обов\'язковим полем';
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Невірний формат email';
    }

    // Якщо помилок немає — зберігаємо
    if (empty($errors)) {
        $comment = [
                'email' => $email,
                'name'  => $name,
                'text'  => $text,
                'date'  => date('d.m.Y H:i')
        ];

        // Запис у файл
        $jsonString = json_encode($comment, JSON_UNESCAPED_UNICODE);
        $fileStream = fopen($commentsFile, 'a');
        fwrite($fileStream, $jsonString . "\n");
        fclose($fileStream);

        $success = true;
    }
}

// Отримуємо коментарі для відображення
$comments = getComments($commentsFile);
// Показуємо спочатку нові
$comments = array_reverse($comments);
?>
<!DOCTYPE html>
<html>
<?php require_once 'sectionHead.php' ?>
<body>
<div class="container">

    <!-- navbar menu -->
    <?php require_once 'sectionNavbar.php' ?>

    <br>

    <!-- guestbook form section -->
    <div class="card card-primary">
        <div class="card-header bg-primary text-light">
            GuestBook form
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">

                    <!-- Повідомлення про успіх -->
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            Коментар успішно додано!
                        </div>
                    <?php endif; ?>

                    <!-- Помилки валідації -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <div><?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- TODO: create guestBook html form -->
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

    <!-- comments section -->
    <div class="card card-primary">
        <div class="card-header bg-body-secondary text-dark">
            Comments
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">

                    <!-- TODO: render guestBook comments -->
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