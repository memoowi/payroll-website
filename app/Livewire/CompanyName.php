<?php

namespace App\Livewire;

use App\Models\CompanySetting;
use Livewire\Component;

class CompanyName extends Component
{
    public $companyName;

    public function mount()
    {
        $this->companyName = CompanySetting::first()->name;
    }
    public function render()
    {
        return <<<'HTML'
        <span class="mb-0.5 truncate leading-none text-xs text-accent-content">
           {{ $this->companyName}}
        </span>
        HTML;
    }
}
