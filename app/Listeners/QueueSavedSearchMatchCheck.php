<?php

namespace App\Listeners;

use App\Events\PropertyPublished;
use App\Jobs\NotifySavedSearchMatches;
use App\Models\SavedSearch;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Queues a saved-search match sweep whenever a listing goes live.
 */
class QueueSavedSearchMatchCheck implements ShouldQueue
{
    public function handle(PropertyPublished $event): void
    {
        $matching = SavedSearch::query()
            ->where('notify', true)
            ->pluck('id');

        foreach ($matching as $savedSearchId) {
            NotifySavedSearchMatches::dispatch($savedSearchId, $event->property->id);
        }
    }
}
