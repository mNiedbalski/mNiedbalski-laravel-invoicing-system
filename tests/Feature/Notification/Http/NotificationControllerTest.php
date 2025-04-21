<?php

declare(strict_types=1);

namespace Tests\Feature\Notification\Http;

use Illuminate\Foundation\Testing\WithFaker;
use Modules\Invoices\Domain\Entities\Customer;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\ProductLine;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        $this->setUpFaker();

        parent::setUp();
    }

    #[DataProvider('hookActionProvider')]
    public function testHook(string $action): void
    {
        $customer = new Customer(name: $this->faker->name, email: $this->faker->email);
        $productLines = [new ProductLine(
            name: $this->faker->word,
            quantity: $this->faker->numberBetween(1, 10),
            unitPrice: new Money($this->faker->numberBetween(1, 10)),
        )];

        $invoice = new Invoice(
            customer: $customer,
            status: StatusEnum::Draft,
            productLines: $productLines,
            id: $this->faker->uuid,
        );
        $invoiceAdapter = app(\Modules\Invoices\Infrastructure\Adapters\InvoiceAdapter::class);
        $invoiceAdapter->create($invoice);

        $uri = route('notification.hook', [
            'action' => $action,
            'reference' => $invoice->getId(),
        ]);

        $this->getJson($uri)->assertOk();
    }

    public function testInvalid(): void
    {
        $params = [
            'action' => 'dummy',
            'reference' => $this->faker->numberBetween(),
        ];

        $uri = route('notification.hook', $params);
        $this->getJson($uri)->assertNotFound();
    }

    public static function hookActionProvider(): array
    {
        return [
            ['delivered'],
            ['dummy'],
        ];
    }
}
