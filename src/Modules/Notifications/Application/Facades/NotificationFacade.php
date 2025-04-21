<?php

declare(strict_types=1);

namespace Modules\Notifications\Application\Facades;

use Illuminate\Support\Facades\Http;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Api\NotificationFacadeInterface;
use Modules\Notifications\Infrastructure\Drivers\DummyDriver;

final readonly class NotificationFacade implements NotificationFacadeInterface
{
    public function __construct(
        private DummyDriver $driver, // Changed because DriverInterface can't be instantiated
        // I had to use this solution with host.docker.internal:8080 because there were some errors.
        // There is a chance it was due to my custom 8080 port mapping in docker-compose.yml (WSL on Windows problems with port 8000 occupied)
        private string $baseUrl = 'http://host.docker.internal:8080' // Changed to use the base URL for the notification hook
    ) {}

    public function notify(NotifyData $data): void
    {
        $sent = $this->driver->send(
            toEmail: $data->toEmail,
            subject: $data->subject,
            message: $data->message,
            reference: $data->resourceId->toString(),
        );
        if ($sent) {
            $action = 'delivered';
            $reference = $data->resourceId->toString();
            $routeUrl = route('notification.hook', ['action' => $action, 'reference' => $reference], false); // Generate relative path
            $routeUrl = $this->baseUrl . $routeUrl; // Prepend the correct host and port
            Http::get($routeUrl);
        } else {
            throw new \DomainException('Notification not sent');
        }

    }
}
