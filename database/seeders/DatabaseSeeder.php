<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Division;
use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('divisions')->insert([
            ['id' => Str::uuid(), 'name' => 'Mobile Apps'],
            ['id' => Str::uuid(), 'name' => 'QA'],
            ['id' => Str::uuid(), 'name' => 'Full Stack'],
            ['id' => Str::uuid(), 'name' => 'Backend'],
            ['id' => Str::uuid(), 'name' => 'Frontend'],
            ['id' => Str::uuid(), 'name' => 'UI/UX Designer'],
        ]);

        User::factory(1)->create([
            'username' => 'Test User',
            'password'=> bcrypt('password'),
        ]);

        $divisions = \App\Models\Division::all();

        Employee::factory(50)->make()->each(function ($employee) use ($divisions) {
            $employee->division_id = $divisions->random()->id;
            $employee->save();
        });
    }
}
