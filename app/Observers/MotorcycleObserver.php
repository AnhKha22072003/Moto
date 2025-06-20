<?php
namespace App\Observers;

use App\Models\Motorcycle;
use App\Models\MotorcycleLog;
use Illuminate\Support\Facades\Auth;

class MotorcycleObserver
{
    public function created(Motorcycle $motorcycle)
    {
        MotorcycleLog::create(attributes: [
            'motorcycle_id' => $motorcycle->id,
            'event'         => 'created',
            'changed_by'    => Auth::id() ?? 'system',
        ]);
    }

    public function updated(Motorcycle $motorcycle)
    {
        $changes = $motorcycle->getChanges();
        $original = $motorcycle->getOriginal();

        foreach ($changes as $field => $newValue) {
            if ($field === 'updated_at') continue;

            MotorcycleLog::create([
                'motorcycle_id' => $motorcycle->id,
                'event'         => 'updated',
                'field'         => $field,
                'old_value'     => $original[$field] ?? null,
                'new_value'     => $newValue,
                'changed_by'    => Auth::id() ?? 'system',
            ]);
        }
    }

    public function deleting(Motorcycle $motorcycle)
    {
        MotorcycleLog::create([
            'motorcycle_id' => $motorcycle->id,
            'event'         => 'deleted',
            'changed_by'    => Auth::id() ?? 'system',
        ]);
    }

    public function restored(Motorcycle $motorcycle)
    {
        MotorcycleLog::create([
            'motorcycle_id' => $motorcycle->id,
            'event'         => 'restored',
            'changed_by'    => Auth::id() ?? 'system',
        ]);
    }

    public function forceDeleted(Motorcycle $motorcycle)
    {
        MotorcycleLog::create([
            'motorcycle_id' => $motorcycle->id,
            'event'         => 'forceDeleted',
            'changed_by'    => Auth::id() ?? 'system',
        ]);
    }
}
