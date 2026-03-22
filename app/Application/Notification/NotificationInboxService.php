<?php

declare(strict_types=1);

namespace Intranet\Application\Notification;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Alumno;
use Intranet\Entities\Notification;

/**
 * Casos d'ús per a la safata de notificacions d'usuari.
 */
class NotificationInboxService
{
    private ?ProfesorService $profesorService;

    public function __construct(?ProfesorService $profesorService = null)
    {
        $this->profesorService = $profesorService;
    }

    /**
     * @return EloquentCollection<int, Notification>
     */
    public function listForUser(object $user): EloquentCollection
    {
        $userKey = $user->primaryKey;

        return Notification::where('notifiable_id', '=', (string) $user->$userKey)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function markAsRead(int|string $id): bool
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return false;
        }

        $notification->read_at = now();
        $notification->save();

        return true;
    }

    public function markAllAsRead(object $user): bool
    {
        $notifiable = $this->resolveNotifiable($user);
        if (!$notifiable) {
            return false;
        }

        $notifiable->unreadNotifications->markAsRead();
        return true;
    }

    public function deleteAll(object $user): bool
    {
        $notifiable = $this->resolveNotifiable($user);
        if (!$notifiable) {
            return false;
        }

        $notifiable->notifications()->delete();
        return true;
    }

    public function deleteById(int|string $id): void
    {
        if ($notification = Notification::find($id)) {
            $notification->delete();
        }
    }

    public function findForShow(int|string $id): ?Notification
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return null;
        }

        return $this->hydratePayload($notification);
    }

    private function profesores(): ProfesorService
    {
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }

        return $this->profesorService;
    }

    private function resolveNotifiable(object $user): mixed
    {
        $userKey = $user->primaryKey;
        if ($userKey === 'dni') {
            return $this->profesores()->find((string) $user->$userKey);
        }

        return Alumno::find($user->$userKey);
    }

    private function hydratePayload(Notification $notification): Notification
    {
        $payload = json_decode((string) $notification->data, true);
        if (!is_array($payload)) {
            return $notification;
        }

        foreach ($payload as $key => $value) {
            if (is_string($key) && !is_array($value) && !is_object($value)) {
                $notification->setAttribute($key, $value);
            }
        }

        return $notification;
    }
}
