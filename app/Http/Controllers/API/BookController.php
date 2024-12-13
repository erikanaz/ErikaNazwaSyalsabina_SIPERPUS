<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use function Laravel\Prompts\error;

class BookController extends Controller
{
    public function Books()
    {
        try {
            $books = Book::all();
            return response()->json([
                'message' => 'Get all book Success',
                'books' => $books
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Request Failed',
                'error' => $e->getMessage()
            ], 401);
        }
    }
    public function Book($id)
    {
        try {
            $book = Book::findOrFail($id);
            return response()->json([
                'message' => 'Get book by ID Success',
                'book' => $book
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Book not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|max:255',
                'author' => 'required|max:150',
                'year' => 'required|digits:4|integer|min:1900|max:' . (date('Y')),
                'publisher' => 'required|max:100',
                'city' => 'required|max:75',
                'bookshelf_id' => 'required',
                'cover' => 'nullable|image',
            ]);
            if ($request->hasFile('cover')) {
                $path = $request->file('cover')->storeAs(
                    'public/cover_buku',
                    'cover_buku_' . time() . '.' . $request->file('cover')->extension()
                );
                $validated['cover'] = basename($path);
            }
            $book = Book::create($validated);
            return response()->json([
                'message' => 'Create book Success',
                'book' => $book
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Request Failed',
                'error' => $e->getMessage()
            ], 401);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $book = Book::findOrFail($id);
            if ($request->method() == 'PUT') {
                $validated = $request->validate([
                    'title' => 'required|max:255',
                    'author' => 'required|max:150',
                    'year' => 'required|digits:4|integer|min:1900|max:' . (date('Y')),
                    'publisher' => 'required|max:100',
                    'city' => 'required|max:75',
                    'bookshelf_id' => 'required',
                    'cover' => 'nullable|image|file',
                ]);
            } else {
                $validated = $request->validate([
                    'title' => 'sometimes|required|max:255',
                    'author' => 'sometimes|required|max:150',
                    'year' => 'sometimes|required|digits:4|integer|min:1900|max:' . (date('Y')),
                    'publisher' => 'sometimes|required|max:100',
                    'city' => 'sometimes|required|max:75',
                    'bookshelf_id' => 'sometimes|required',
                    'cover' => 'nullable|image|file',
                ]);
            }
            if ($request->hasFile('cover')) {
                if ($book->cover != null) {
                    Storage::delete('public/cover_buku/' . $request->old_cover);
                }
                $path = $request->file('cover')->storeAs(
                    'public/cover_buku',
                    'cover_buku_' . time() . '.' . $request->file('cover')->extension()
                );
                $validated['cover'] = basename($path);
            }
            Book::where('id', $id)->update($validated);
            $res = Book::find($id);
            return response()->json([
                'message' => 'Update book Success',
                'book' => $res
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Request Failed',
                'error' => $e->getMessage()
            ], 401);
        }
    }
    public function destroy($id)
    {
        try {
            $book = Book::findOrFail($id);
            Storage::delete('public/cover_buku/' . $book->cover);
            $book->delete();
            return response()->json([
                'message' => 'Delete book Success',
                'book' => $book
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Book not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
