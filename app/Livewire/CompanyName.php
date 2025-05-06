<?php

namespace App\Livewire;

use App\Models\CompanySetting;
use Livewire\Attributes\On;
use Livewire\Component;

class CompanyName extends Component
{
    public $companyName = '';

    public function mount()
    {
        $this->getCompanyName();
    }
    public function render()
    {
        return <<<'HTML'
        <span class="mb-0.5 truncate leading-none text-xs text-accent-content">
           {{ $this->companyName}}
        </span>
        HTML;
    }
    #[On('updated-company-setting')]
    public function getCompanyName()
    {
        $this->companyName = CompanySetting::first()->name;
    }
}
