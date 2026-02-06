<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $totalReports;
    public $statusCounts = [];
    public $recentReports;

    protected $listeners = ['reportUpdated' => 'refreshData'];

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $user = Auth::user();

        $this->totalReports = Report::count();
        $this->statusCounts = Report::select('status')
            ->selectRaw('count(*) as total')
            ->groupBy('status')
            ->pluck('total','status')
            ->toArray();

        $this->recentReports = Report::with('user','reviewer')
            ->where('user_id',$user->id)
            ->orWhereHas('user', function($q) use($user){
                $q->where('parent_id',$user->id);
            })
            ->latest()
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
