# mNiedbalski additions:

## Running project

#### Run those in the first terminal:
* `./start.sh`
* `docker compose exec app bash` <br>
* `php artisan queue:work redis` <br>
#### Launch second terminal and type in:
* `docker compose exec app bash`
* `php artisan serve` <br>

#### Database
To monitor database changes, double-click on the SQLite file in the `storage` folder.

#### Access page

Enter [127.0.0.1:8080](http://127.0.0.1:8080) in your browser to access the application.

## Project decisions
### Configuration changes

Since I was working on Windows, I had to change the port in the docker-compose.yml file to 8080. (WSL conflicts with port 8000)
* `- '${APP_PORT:-8080}:80'` -- Line 12, docker-compose.yml

I've encountered errors connected with file permissions for logger so I had to run those commands:
* `chmod -R guo+w storage`
* `php artisan cache:clear`

### Price/Amount/Money representation
Instead of using integer type for prices, I have decided to introduce a new class called `Money` to handle monetary values.
This solution is more robust and allows for better handling of currency-related operations, such as formatting and arithmetic.

### Autogenerating UUID
I've decided to add ramsey/uuid library for UUID generation, as it is a widely used library for this purpose in PHP.
It is better to use a library that is well-tested and widely adopted rather than implementing a custom solution.

### ProductLine extensions
I have also decided to add taxRate and discountRate fields to the ProductLine class, as these are common fields in invoice systems.
They won't be interfering with core functionality, but they will allow for more flexibility in the future.
Following those changes, I've added additional methods in Invoice class that return taxed, discounted and taxed+discounted amounts.

### Schema additions
Following DDD principles, I have decided to create a separate class for the Customer entity.

### Sending invoices
To handle the sending of invoices, I have created a new service called `InvoiceNotificationService` that is responsible for sending the invoice to the customer.
This service uses NotificationFacade to send the invoice, but also encapsulates specific logic related to sending invoices.

### Event handling

I have added an `InvoiceEventServiceProvider` and `UpdateInvoiceStatusListener` to handle the event of invoice status update.
I'm using Redis work queue to handle the event asynchronously (even though it's a simple case, I wanted to prepare event handler for more advanced operations that shouldn't be done synchronously).

### Database

Since there are endpoints that may suggest storing invoice data somewhere, I have integrated application with SQLite database that was pre-connected.
I have created ORM Eloquent models for data, with mutators and accessors (for example Money class) for the fields that require special handling.
Finally, I have also added migrations for the database tables because some columns had to be altered or added.

### Omitted functionality

It wasn't specified whether creating an invoice should be done from form data, therefore I have hardcoded needed values in controller and createInvoice button acts as trigger.

## Difficulties

* There were some errors connected with `DriverInterface` and I decided to use `DummyDriver`. 
* Another problem was with `NotificationFacade` and url handling -- I had to use http://host.docker.internal:8080 instead of `127.0.0.1:8080`. 
I suspect it might have been caused by the fact that I was working on Windows and WSL had some conflicts.

## Testing

### Endpoints
Endpoints were tested manually and I have added PHPUnit test for EventListener which is commented out (that's why warning will be displayed after running tests).

### Core logic
Core logic was thoroughly tested with PHPUnit tests.
# Task description

## Invoice Structure:

The invoice should contain the following fields:
* **Invoice ID**: Auto-generated during creation.
* **Invoice Status**: Possible states include `draft,` `sending,` and `sent-to-client`.
* **Customer Name** 
* **Customer Email** 
* **Invoice Product Lines**, each with:
  * **Product Name**
  * **Quantity**: Integer, must be positive. 
  * **Unit Price**: Integer, must be positive.
  * **Total Unit Price**: Calculated as Quantity x Unit Price. 
* **Total Price**: Sum of all Total Unit Prices.

## Required Endpoints:

1. **View Invoice**: Retrieve invoice data in the format above.
2. **Create Invoice**: Initialize a new invoice.
3. **Send Invoice**: Handle the sending of an invoice.

## Functional Requirements:

### Invoice Criteria:

* An invoice can only be created in `draft` status. 
* An invoice can be created with empty product lines. 
* An invoice can only be sent if it is in `draft` status. 
* An invoice can only be marked as `sent-to-client` if its current status is `sending`. 
* To be sent, an invoice must contain product lines with both quantity and unit price as positive integers greater than **zero**.

### Invoice Sending Workflow:

* **Send an email notification** to the customer using the `NotificationFacade`. 
  * The email's subject and message may be hardcoded or customized as needed. 
  * Change the **Invoice Status** to `sending` after sending the notification.

### Delivery:

* Upon successful delivery by the Dummy notification provider:
  * The **Notification Module** triggers a `ResourceDeliveredEvent` via webhook.
  * The **Invoice Module** listens for and captures this event.
  * The **Invoice Status** is updated from `sending` to `sent-to-client`.
  * **Note**: This transition requires that the invoice is currently in the `sending` status.

## Technical Requirements:

* **Preferred Approach**: Domain-Driven Design (DDD) is preferred for this project. If you have experience with DDD, please feel free to apply this methodology. However, if you are more comfortable with another approach, you may choose an alternative structure.
* **Alternative Submission**: If you have a different, comparable project or task that showcases your skills, you may submit that instead of creating this task.
* **Unit Tests**: Core invoice logic should be unit tested. Testing the returned values from endpoints is not required.
* **Documentation**: Candidates are encouraged to document their decisions and reasoning in comments or a README file, explaining why specific implementations or structures were chosen.

## Setup Instructions:

* Start the project by running `./start.sh`.
* To access the container environment, use: `docker compose exec app bash`.
