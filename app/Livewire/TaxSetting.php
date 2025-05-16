<?php

namespace App\Livewire;

use App\Models\Tax;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class TaxSetting extends Component
{
    use WithPagination;
    public $name = '';
    public $description = '';
    public $rate = 0;
    public $lowerBound = '';
    public $upperBound = '';
    public $isEditting = false;
    public $selectedId = null;
    #[Title('Tax Setting')]
    public function render()
    {
        return view('livewire.admin.tax-setting', [
            'taxes' => Tax::latest()->paginate(10,['id', 'name', 'rate', 'threshold']),
        ]);
    }
    public function closeModal()
    {
        $this->reset();
    }
    public function openModal($modalType, $selectedId = null)
    {
        $this->isEditting = $modalType === 'edit';
        if ($this->isEditting) {
            $this->selectedId = $selectedId;
            $tax = Tax::find($selectedId);
            if ($tax) {
                $this->name = $tax->name;
                $this->description = $tax->description;
                $this->rate = $tax->rate;
                // Assuming the threshold is stored as a string in the format "lowerBound-upperBound"
                // You can adjust the parsing logic based on your actual data format
                $thresholds = explode('-', $tax->threshold);
                $this->lowerBound = $thresholds[0];
                $this->upperBound = $thresholds[1] ?? ''; 
            }
        }
        $this->modal('main-modal')->show();
    }
    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:taxes,name,' . $this->selectedId,
            'description' => 'nullable|string|max:1000',
            'rate' => 'required|numeric|min:0|max:1',
            'lowerBound' => 'required|numeric',
            'upperBound' => 'required|numeric|gte:lowerBound',
        ]);

        $threshold = $this->lowerBound . '-' . ($this->upperBound ?: '');

        if ($this->isEditting) {
            $tax = Tax::find($this->selectedId);
            if ($tax) {
                $tax->update([
                    'name' => $this->name,
                    'description' => $this->description,
                    'rate' => $this->rate,
                    'threshold' => $threshold,
                ]);
            }
        } else {
            Tax::create([
                'name' => $this->name,
                'description' => $this->description,
                'rate' => $this->rate,
                'threshold' => $threshold,
            ]);
        }
        Toaster::success('Tax setting saved successfully!');
        $this->closeModal();
        $this->modal('main-modal')->close();
        $this->resetPage();
    }
    public function openDeleteModal($id, $name)
    {
        $this->selectedId = $id;
        $this->name = $name;
        $this->modal('delete-modal')->show();
    }
    public function deleteTax()
    {
        Tax::find($this->selectedId)->delete();
        Toaster::success('Tax deleted successfully');
        $this->closeModal();
        $this->modal('delete-modal')->close();
        $this->resetPage();
    }
}
