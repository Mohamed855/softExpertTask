<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Task::first()) {
            $user = User::where('role', 'user')->first();
            $manager = User::where('role', 'manager')->first();
            for($i = 1; $i <= 5; $i++) {
                Task::create([
                    'name' => 'Task ' . $i,
                    'description' => 'Task details ' . $i,
                    'assigned_to' => $user->id,
                    'due_date' => now()->addDays($i),
                    'assigned_by' => $manager->id
                ]);
            }
            DB::table('task_dependencies')->insert([
                ['task_id' => 4, 'depends_on_id' => 1],
                ['task_id' => 4, 'depends_on_id' => 2],
                ['task_id' => 5, 'depends_on_id' => 3]
            ]);
        }
    }
}
