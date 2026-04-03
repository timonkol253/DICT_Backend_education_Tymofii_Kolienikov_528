<?php
namespace guestbook\Controllers;

class GuestbookController
{
    public function execute(): void
    {
        $pdo = new \PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
            DB_USER,
            DB_PASS
        );
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $successMessage = '';
        $errorMessage   = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $name  = trim($_POST['name']  ?? '');
            $text  = trim($_POST['text']  ?? '');

            if (empty($email) || empty($name) || empty($text)) {
                $errorMessage = 'Будь ласка, заповніть всі поля.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorMessage = 'Некоректний email.';
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO comments (name, email, text, date) VALUES (:name, :email, :text, NOW())'
                );
                $stmt->execute([':name' => $name, ':email' => $email, ':text' => $text]);
                $successMessage = 'Коментар успішно додано!';
            }
        }

        // Отримати всі коментарі
        $stmt     = $pdo->query('SELECT * FROM comments ORDER BY date DESC');
        $comments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->renderView([
            'comments'       => $comments,
            'successMessage' => $successMessage,
            'errorMessage'   => $errorMessage,
        ]);
    }

    public function renderView(array $arguments = []): void
    {
        $comments       = $arguments['comments']       ?? [];
        $successMessage = $arguments['successMessage'] ?? '';
        $errorMessage   = $arguments['errorMessage']   ?? '';
        ?>
        <?php include 'sectionHead.php'; ?>
        <body>
        <?php include 'sectionNavbar.php'; ?>

        <div class="container mt-4">
            <h2>Гостьова книга</h2>

            <?php if ($successMessage): ?>
                <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>
            <?php if ($errorMessage): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>

            <form method="POST" action="/guestbook">
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Ім'я</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Коментар</label>
                    <textarea name="text" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Відправити</button>
            </form>

            <hr>
            <h4>Коментарі:</h4>
            <?php foreach ($comments as $comment): ?>
                <div class="card mb-2">
                    <div class="card-body">
                        <strong><?= htmlspecialchars($comment['name']) ?></strong>
                        (<?= htmlspecialchars($comment['email']) ?>)
                        <span class="text-muted float-end"><?= $comment['date'] ?></span>
                        <p class="mt-2 mb-0"><?= htmlspecialchars($comment['text']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        </body>
        </html>
        <?php
    }
}