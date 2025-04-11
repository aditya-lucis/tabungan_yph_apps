<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgramLevel extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Program::insert([
            ['level' => 'SD', 'total' => 1250000],
            ['level' => 'SMP', 'total' => 1250000],
            ['level' => 'SMA', 'total' => 1250000],
            ['level' => 'Perguruan Tinggi', 'total' => 2000000],
        ]);
        
    }
}
