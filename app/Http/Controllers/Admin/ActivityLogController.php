<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Activity::query()->with('causer')->latest();

        if (filled($request->query('event'))) {
            $query->where('event', $request->query('event'));
        }

        if (filled($request->query('q'))) {
            $q = $request->query('q');
            $query->where(fn ($inner) => $inner
                ->where('description', 'like', '%'.$q.'%')
                ->orWhere('subject_type', 'like', '%'.$q.'%'));
        }

        $events = Activity::query()->select('event')->distinct()->orderBy('event')->pluck('event');

        return view('admin.activity-log', [
            'activities' => $query->paginate(30)->withQueryString(),
            'events' => $events,
            'activeEvent' => $request->query('event'),
        ]);
    }
}
