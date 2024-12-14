<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    //
    public function search(Request $request)
    {
        $query = $request->input('query');

        // Pencarian buku
        $books = Book::where('title', 'like', '%' . $query . '%')
            ->orWhere('author', 'like', '%' . $query . '%')
            ->get();

        // Pencarian pengarang (jika Anda punya model Author)
        $authors = User::where('name', 'like', '%' . $query . '%')->get();

        // Kembalikan data dalam format JSON
        return response()->json([
            'books' => $books,
            'authors' => $authors
        ]);
    }
}
