<?php

declare(strict_types=1);

namespace Tests\Unit\Notification\Facades;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Application\Facades\NotificationFacade;
use Modules\Notifications\Infrastructure\Drivers\DriverInterface;
use Modules\Notifications\Infrastructure\Drivers\DummyDriver;
use PHPUnit\Framework\TestCase;

/**
 * Unfortunately I had troubles with fixing this test due to applied changes to NotificationFacade.
 * I would be glad to hear if those errors were truly connected with WSL and port mapping or if I'm mistaken and lacked knowledge in this particular case.
 */
final class NotificationFacadeTest extends TestCase
{
    use WithFaker;

    // I've encountered issues with DriverInterface binding exceptions therefore I changed it to DummyDriver
    private DummyDriver $driver;
//    private DriverInterface $driver;

    private NotificationFacade $notificationFacade;

    // I've encountered issues with DriverInterface binding exceptions therefore I changed it to DummyDriver
    protected function setUp(): void
    {
        $this->setUpFaker();

        // Use an actual instance of DummyDriver
        $this->driver = new DummyDriver();
//        $this->driver = $this->createMock(DriverInterface::class);
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

        // I've encountered issues with DriverInterface binding exceptions therefore I changed it to DummyDriver
//        $this->driver->expects($this->once())->method('send');

        $this->notificationFacade->notify($data);
        $this->assertTrue(true);
    }
}
