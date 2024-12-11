<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Book::create([
            "title" => "Jarkom - Filosofi",
            "author" => "GTA",
            "year" => 2024,
            "publisher" => "UNSUR Mengantuk",
            "city" => "Cianjir",
            "cover" => "public/cover.jpg",
            "bookshelf_id" => 2
        ]);
    }
}
