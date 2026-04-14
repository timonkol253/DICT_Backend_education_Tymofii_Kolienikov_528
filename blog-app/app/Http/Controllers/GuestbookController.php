<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestbookController extends Controller
{
    // GET-запит: відображення сторінки + список коментарів
    public function index()
    {
        $comments = DB::table('comments')
            ->orderBy('date', 'desc')
            ->get();

        return view('guestbook', compact('comments'));
    }

    // POST-запит: обробка форми та збереження в БД
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'text'  => 'required|string',
        ]);

        DB::table('comments')->insert([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'text'  => $validated['text'],
            'date'  => date('Y-m-d H:i:s'), // стандартний формат MySQL
        ]);

        return redirect()->route('guestbook')->with('success', 'Коментар успішно додано!');
    }
}
