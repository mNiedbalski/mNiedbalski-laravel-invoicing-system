<?php

declare(strict_types=1);

namespace Modules\Notifications\Application\Facades;

use Illuminate\Support\Facades\Http;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Api\NotificationFacadeInterface;
use Modules\Notifications\Infrastructure\Drivers\DriverInterface;
use Modules\Notifications\Infrastructure\Drivers\DummyDriver;

final readonly class NotificationFacade implements NotificationFacadeInterface
{
    public function __construct(
        private DummyDriver $driver, // Changed because DriverInterface can't be instantiated
    ) {}

    public function notify(NotifyData $data): void
    {
        $isSending = $this->driver->send(
            toEmail: $data->toEmail,
            subject: $data->subject,
            message: $data->message,
            reference: $data->resourceId->toString(),
        );
        if ($isSending){
            $action = 'delivered';
            $reference = $data->resourceId->toString();
            $routeUrl = route('notification.hook', ['action' => $action, 'reference' => $reference], false); // Generate relative path
            $routeUrl = 'http://host.docker.internal:8080' . $routeUrl; // Prepend the correct host and port
            Http::get($routeUrl);
        }

    }
}
