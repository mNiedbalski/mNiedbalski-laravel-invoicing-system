<?php

declare(strict_types=1);

namespace Tests\Unit\Notification\Facades;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Application\Facades\NotificationFacade;
use Modules\Notifications\Infrastructure\Drivers\DummyDriver;
use Illuminate\Foundation\Testing\TestCase;

/**
 * Unfortunately I had troubles with fixing this test due to applied changes to NotificationFacade.
 * I would be glad to hear if those errors were truly connected with WSL and port mapping or if I'm mistaken and lacked knowledge in this particular case.
 */
final class NotificationFacadeTest extends TestCase
{
    use WithFaker;

    private DummyDriver $driver;
    private NotificationFacade $notificationFacade;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();

        // Mock the Http facade
        Http::fake();

        $this->driver = new DummyDriver();
        $this->notificationFacade = new NotificationFacade(
            driver: $this->driver,
        );
    }

    public function testDelivered(): void
    {
        $data = new NotifyData(
            resourceId: Str::uuid(),
            toEmail: $this->faker->email(),
            subject: $this->faker->sentence(),
            message: $this->faker->sentence(),
        );

        $this->notificationFacade->notify($data);

        // Assert that the Http::get method was called
        Http::assertSent(function ($request) use ($data) {
            return $request->url() === 'http://host.docker.internal:8080/api/notification/hook/delivered/' . $data->resourceId->toString();
        });
    }
}
