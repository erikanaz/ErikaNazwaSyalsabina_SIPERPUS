<?php

namespace Database\Seeders;

use App\Models\Bookshelf;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookshelfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //query builder
        DB::table('bookshelves')->insert([
            [
                'code'=>'RAK01',
                'name'=>'Informatika',
            ],
            [
                'code'=>'RAK02',
                'name'=>'Sipil',
            ],
            [
                'code'=>'GTA03',
                'name'=>'Filsafat',
            ]
        ]);

        //ORM -> Object Relational Model
        Bookshelf::create([
            'code'=>'RAK03',
            'name'=>'Industri',
        ]);
    }
}
