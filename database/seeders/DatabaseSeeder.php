<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        User::factory()->count(200)->create();

        User::query()->inRandomOrder()->limit(10)->get()->each(
            function ($user) {
                $project = Project::factory()->create(['created_by' => $user->id]);
                Proposal::factory()->count(random_int(4, 40))->create(['project_id' => $project->id]);
                DB::update('with RankedProposals as (
                    select id,
                    row_number() over (order by hours asc) as p
                    from proposals
                    where project_id = :project_id
                    )
                update proposals
                set position = (select p from RankedProposals where proposals.id = RankedProposals.id)
                where project_id = :project_id', ['project_id' => $project->id]);
            }
        );
    }
}
