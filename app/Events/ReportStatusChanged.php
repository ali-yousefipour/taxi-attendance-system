<?php

namespace App\Events;

use App\Models\Report;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ReportStatusChanged implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $report;
    public $message;

    public function __construct(Report $report, $message)
    {
        $this->report = $report;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('reports.' . $this->report->user_id);
    }

    public function broadcastWith()
    {
        return [
            'report_id' => $this->report->id,
            'title' => $this->report->title,
            'status' => $this->report->status,
            'message' => $this->message
        ];
    }
}
