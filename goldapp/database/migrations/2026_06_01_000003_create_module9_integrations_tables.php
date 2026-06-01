<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('api_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('client_key', 80)->unique();
            $table->string('client_secret', 255);
            $table->json('permissions')->nullable();
            $table->integer('rate_limit_per_minute')->default(60);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('last_used_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('endpoint', 255);
            $table->string('method', 10);
            $table->json('request_body')->nullable();
            $table->integer('response_code');
            $table->integer('response_time_ms');
            $table->string('ip_address', 45);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('integration_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('integration_type', ['gst','payment_gateway','courier','ecommerce','accounting','bi']);
            $table->string('provider', 80);
            $table->json('config')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
        });

        Schema::create('gst_filings', function (Blueprint $table) {
            $table->id();
            $table->string('period_name', 20);
            $table->date('from_date');
            $table->date('to_date');
            $table->enum('filing_type', ['GSTR1','GSTR3B','GSTR9']);
            $table->decimal('total_taxable', 18, 2)->default(0);
            $table->decimal('total_sgst', 18, 2)->default(0);
            $table->decimal('total_cgst', 18, 2)->default(0);
            $table->decimal('total_igst', 18, 2)->default(0);
            $table->json('json_payload')->nullable();
            $table->string('irn_no', 100)->nullable();
            $table->enum('status', ['draft','ready','filed','error'])->default('draft');
            $table->timestamp('filed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('einvoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_invoice_id');
            $table->string('irn', 100)->unique();
            $table->string('ack_no', 50);
            $table->timestamp('ack_date');
            $table->text('qr_code')->nullable();
            $table->text('signed_invoice')->nullable();
            $table->string('cancel_irn', 100)->nullable();
            $table->timestamp('cancel_date')->nullable();
            $table->enum('status', ['active','cancelled'])->default('active');
            $table->timestamps();
        });

        Schema::create('eway_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_invoice_id');
            $table->string('ewb_no', 20)->unique();
            $table->timestamp('ewb_date');
            $table->timestamp('valid_till');
            $table->string('from_gstin', 30);
            $table->string('to_gstin', 30);
            $table->string('transporter_id', 30)->nullable();
            $table->string('vehicle_no', 20)->nullable();
            $table->integer('distance_km')->nullable();
            $table->enum('status', ['active','cancelled','extended'])->default('active');
            $table->timestamps();
        });

        Schema::create('payment_gateway_txns', function (Blueprint $table) {
            $table->id();
            $table->string('order_ref', 80);
            $table->enum('gateway', ['razorpay','paytm','stripe','paypal','upi']);
            $table->string('gateway_txn_id', 120)->unique();
            $table->decimal('amount', 18, 2);
            $table->string('currency', 10)->default('INR');
            $table->enum('status', ['created','processing','success','failed','refunded'])->default('created');
            $table->unsignedBigInteger('sales_invoice_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->json('response_data')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('courier_shipments', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique();
            $table->unsignedBigInteger('dispatch_id')->nullable();
            $table->enum('courier_partner', ['delhivery','dtdc','bluedart','fedex','ecom_express','custom']);
            $table->string('awb_no', 80)->nullable();
            $table->decimal('weight', 10, 3);
            $table->string('dimensions', 50)->nullable();
            $table->decimal('charges', 18, 2)->default(0);
            $table->date('pickup_date')->nullable();
            $table->date('estimated_delivery')->nullable();
            $table->date('actual_delivery')->nullable();
            $table->enum('status', ['booked','picked','in_transit','out_for_delivery','delivered','returned','failed'])->default('booked');
            $table->json('tracking_events')->nullable();
            $table->timestamps();
        });

        Schema::create('ecom_channel_orders', function (Blueprint $table) {
            $table->id();
            $table->enum('channel', ['website','amazon','flipkart','swiggy','zomato','shopify','woocommerce']);
            $table->string('channel_order_id', 100)->unique();
            $table->dateTime('order_date');
            $table->string('customer_name', 180);
            $table->string('customer_email', 150)->nullable();
            $table->string('customer_phone', 20)->nullable();
            $table->decimal('total_amount', 18, 2);
            $table->enum('status', ['new','processing','shipped','delivered','cancelled','returned'])->default('new');
            $table->unsignedBigInteger('mapped_so_id')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();
        });

        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source', 60);
            $table->string('event_type', 80);
            $table->json('payload')->nullable();
            $table->enum('status', ['received','processed','failed'])->default('received');
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
        Schema::dropIfExists('ecom_channel_orders');
        Schema::dropIfExists('courier_shipments');
        Schema::dropIfExists('payment_gateway_txns');
        Schema::dropIfExists('eway_bills');
        Schema::dropIfExists('einvoices');
        Schema::dropIfExists('gst_filings');
        Schema::dropIfExists('integration_settings');
        Schema::dropIfExists('api_logs');
        Schema::dropIfExists('api_clients');
    }
};
