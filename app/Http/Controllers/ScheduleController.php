<?php

namespace App\Http\Controllers;

use App\Services\SchedulerService;

class ScheduleController extends Controller
{
    public function schedule(SchedulerService $scheduler)
    {
        $schedule = $scheduler->schedule();

        return view('schedule', compact('schedule'));
    }
}