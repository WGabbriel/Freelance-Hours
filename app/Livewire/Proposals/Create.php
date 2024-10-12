<?php

namespace App\Livewire\Proposals;

use App\Actions\ArrangePositions;
use App\Models\Project;
use App\Models\Proposal;
use App\Notifications\NewProposal;
use App\Notifications\PerdeuMane;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;


class Create extends Component
{
    public Project $project;

    public bool $modal = false;

    #[Validate(['required', 'email'])]
    public string $email = '';

    #[Rule(['required', 'numeric', 'gt:0'])]
    public int $hours = 0;

    public bool $agree = false;

    public function save()
    {
        $this->validate();

        if (!$this->agree) {
            $this->addError('agree', 'Você precisa concordar com os termos de uso');

            return;
        }

        DB::transaction(function () {
            $proposal = $this->project->proposals()
                ->updateOrCreate(
                    ['email' => $this->email],
                    ['hours' => $this->hours]
                );

            $this->modal = false;
            $this->dispatch('proposal::created');
        });
}

    public function render()
    {
        return view('livewire.proposals.create');
    }
}
