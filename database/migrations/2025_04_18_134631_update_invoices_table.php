<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', static function (Blueprint $table): void {
            $table->dropColumn(['customer_name', 'customer_email']);

            // Adding foreign key to the customers table because according to DDD Customer is an entity.
            $table->uuid('customer_id')->after('id');
            $table->foreign('customer_id')->references('id')->on('customers');

            // Adding missing total price column after status column for database readability:).
            $table->decimal('total_price', 18, 2)->after('status')->nullable(false);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {

            $table->string('customer_name')->after('id');
            $table->string('customer_email')->after('customer_name');

            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');

            $table->dropColumn('total_price');
        });
    }
};
