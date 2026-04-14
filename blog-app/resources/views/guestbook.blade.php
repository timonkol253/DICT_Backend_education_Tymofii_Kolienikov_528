<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Гостьова книга</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        form { background: #f5f5f5; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; }
        input, textarea { width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #2563eb; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        .comment { border-bottom: 1px solid #eee; padding: 1rem 0; }
        .success { color: #16a34a; font-weight: bold; margin-bottom: 1rem; }
        .meta { color: #666; font-size: 0.85rem; }
    </style>
</head>
<body>
<h1>Гостьова книга</h1>

@if(session('success'))
    <p class="success">{{ session('success') }}</p>
@endif

<form action="{{ route('guestbook.store') }}" method="POST">
    @csrf
    <label>Ім'я:</label>
    <input type="text" name="name" required>
    <label>Email:</label>
    <input type="email" name="email" required>
    <label>Коментар:</label>
    <textarea name="text" rows="4" required></textarea>
    <button type="submit">Надіслати</button>
</form>

<h2>Останні коментарі:</h2>
@forelse($comments as $comment)
    <div class="comment">
        <strong>{{ $comment->name }}</strong> <span class="meta">({{ $comment->email }})</span>
        <div class="meta">{{ $comment->date }}</div>
        <p>{{ $comment->text }}</p>
    </div>
@empty
    <p>Коментарів поки немає. Будьте першим!</p>
@endforelse
</body>
</html>
