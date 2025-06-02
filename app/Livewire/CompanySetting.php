<?php

namespace App\Livewire;

use App\Models\CompanySetting as ModelsCompanySetting;
use Carbon\Carbon;
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
    public $checkInTime = '';
    public $checkOutTime = '';
    public $workingDays = '';

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
            $this->checkInTime = Carbon::parse($companySetting->check_in_time)->format('H:i');
            $this->checkOutTime = Carbon::parse($companySetting->check_out_time)->format('H:i');
            $this->workingDays = $companySetting->working_days;
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
            'checkInTime' => 'required|date_format:H:i',
            'checkOutTime' => 'required|date_format:H:i',
            'workingDays' => 'required|integer|min:5|max:7',
        ]);

        ModelsCompanySetting::updateOrCreate(
            ['id' => $this->id],
            [
                'name' => $this->name,
                'description' => $this->description,
                'address' => $this->address,
                'phone' => $this->phone,
                'value' => $this->value,
                'check_in_time' => $this->checkInTime,
                'check_out_time' => $this->checkOutTime,
                'working_days' => $this->workingDays,
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
            $this->checkInTime = Carbon::parse($data->check_in_time)->format('H:i');
            $this->checkOutTime = Carbon::parse($data->check_out_time)->format('H:i');
            $this->workingDays = $data->working_days;
        } else {
            $this->reset();
        }
    }
}
