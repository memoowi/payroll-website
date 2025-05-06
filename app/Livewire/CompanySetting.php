<?php

namespace App\Livewire;

use App\Models\CompanySetting as ModelsCompanySetting;
use Livewire\Attributes\Title;
use Livewire\Component;

class CompanySetting extends Component
{
    public $id = '';
    public $name = '';
    public $description = '';
    public $address = '';
    public $phone = '';
    public $value = '';

    public function mount()
    {
        $companySetting = ModelsCompanySetting::first();
        if ($companySetting) {
            $this->id = $companySetting->id;
            $this->name = $companySetting->name;
            $this->description = $companySetting->description;
            $this->address = $companySetting->address;
            $this->phone = $companySetting->phone;
            $this->value = $companySetting->value;
        }
    }
    
    #[Title('Company Settings')]
    public function render()
    {
        return view('livewire.admin.company-setting');
    }
    public function updateCompanySetting()
    {
        $this->validate([
            'name' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255|min:5',
            'value' => 'nullable|string|max:255',
        ]);

        ModelsCompanySetting::updateOrCreate(
            ['id' => $this->id],
            [
                'name' => $this->name,
                'description' => $this->description,
                'address' => $this->address,
                'phone' => $this->phone,
                'value' => $this->value,
            ]
        );

        $this->dispatch('updated-company-setting');
    }
    public function resetFields()
    {
        $data = ModelsCompanySetting::first();
        if ($data) {
            $this->id = $data->id;
            $this->name = $data->name;
            $this->description = $data->description;
            $this->address = $data->address;
            $this->phone = $data->phone;
            $this->value = $data->value;
        } else {
            $this->reset();
        }
    }
}
