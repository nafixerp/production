<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fg_warehouse', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('item_id')->nullable();
            $t->string('item_name');
            $t->string('batch_no')->nullable();
            $t->date('expiry_date')->nullable();
            $t->decimal('qty', 12, 3)->default(0);
            $t->string('unit', 20)->default('KG');
            $t->unsignedBigInteger('warehouse_id')->nullable();
            $t->string('location')->nullable();
            $t->timestamps();
        });

        Schema::create('dispatch_plan', function (Blueprint $t) {
            $t->id();
            $t->string('slno', 30)->nullable();
            $t->date('plan_date');
            $t->unsignedBigInteger('route_id')->nullable();
            $t->unsignedBigInteger('vehicle_id')->nullable();
            $t->string('driver_name')->nullable();
            $t->string('status', 20)->default('planned');
            $t->timestamps();
        });

        Schema::create('dispatch_plan_detail', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('plan_id');
            $t->unsignedBigInteger('so_id')->nullable();
            $t->unsignedBigInteger('customer_id')->nullable();
            $t->string('customer_name');
            $t->text('address')->nullable();
            $t->decimal('qty', 12, 3)->default(0);
            $t->string('status', 20)->default('pending');
            $t->timestamps();
        });

        Schema::create('sales_quotation', function (Blueprint $t) {
            $t->id();
            $t->string('slno', 30)->nullable();
            $t->string('quot_no', 30)->nullable();
            $t->date('quot_date');
            $t->unsignedBigInteger('customer_id')->nullable();
            $t->string('customer_name');
            $t->date('valid_till')->nullable();
            $t->decimal('taxable', 14, 2)->default(0);
            $t->decimal('sgst', 14, 2)->default(0);
            $t->decimal('cgst', 14, 2)->default(0);
            $t->decimal('igst', 14, 2)->default(0);
            $t->decimal('net_amount', 14, 2)->default(0);
            $t->string('status', 20)->default('draft');
            $t->text('narration')->nullable();
            $t->timestamps();
        });

        Schema::create('sales_quotation_detail', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('quot_id');
            $t->unsignedBigInteger('item_id')->nullable();
            $t->string('item_name');
            $t->string('hsn', 20)->nullable();
            $t->string('unit', 20)->default('KG');
            $t->decimal('qty', 12, 3)->default(0);
            $t->decimal('rate', 14, 4)->default(0);
            $t->decimal('amount', 14, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('sales_order', function (Blueprint $t) {
            $t->id();
            $t->string('slno', 30)->nullable();
            $t->string('so_no', 30)->nullable();
            $t->date('so_date');
            $t->unsignedBigInteger('customer_id')->nullable();
            $t->string('customer_name');
            $t->date('delivery_date')->nullable();
            $t->decimal('taxable', 14, 2)->default(0);
            $t->decimal('sgst', 14, 2)->default(0);
            $t->decimal('cgst', 14, 2)->default(0);
            $t->decimal('igst', 14, 2)->default(0);
            $t->decimal('net_amount', 14, 2)->default(0);
            $t->string('status', 20)->default('open');
            $t->text('narration')->nullable();
            $t->timestamps();
        });

        Schema::create('sales_order_detail', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('so_id');
            $t->unsignedBigInteger('item_id')->nullable();
            $t->string('item_name');
            $t->string('unit', 20)->default('KG');
            $t->decimal('qty', 12, 3)->default(0);
            $t->decimal('delivered_qty', 12, 3)->default(0);
            $t->decimal('rate', 14, 4)->default(0);
            $t->decimal('amount', 14, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('retail_pos', function (Blueprint $t) {
            $t->id();
            $t->string('slno', 30)->nullable();
            $t->string('pos_no', 30)->nullable();
            $t->date('pos_date');
            $t->unsignedBigInteger('customer_id')->nullable();
            $t->string('customer_name')->nullable();
            $t->unsignedBigInteger('cashier_id')->nullable();
            $t->decimal('taxable', 14, 2)->default(0);
            $t->decimal('discount', 14, 2)->default(0);
            $t->decimal('sgst', 14, 2)->default(0);
            $t->decimal('cgst', 14, 2)->default(0);
            $t->decimal('net_amount', 14, 2)->default(0);
            $t->string('payment_mode', 20)->default('cash');
            $t->decimal('received_amount', 14, 2)->default(0);
            $t->decimal('change_amount', 14, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('retail_pos_detail', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('pos_id');
            $t->unsignedBigInteger('item_id')->nullable();
            $t->string('item_name');
            $t->decimal('qty', 12, 3)->default(0);
            $t->decimal('rate', 14, 4)->default(0);
            $t->decimal('amount', 14, 2)->default(0);
            $t->decimal('discount', 14, 2)->default(0);
            $t->decimal('sgst', 14, 2)->default(0);
            $t->decimal('cgst', 14, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('wholesale_sales', function (Blueprint $t) {
            $t->id();
            $t->string('slno', 30)->nullable();
            $t->string('ws_no', 30)->nullable();
            $t->date('ws_date');
            $t->unsignedBigInteger('customer_id')->nullable();
            $t->string('customer_name');
            $t->string('customer_type', 30)->default('wholesale');
            $t->decimal('taxable', 14, 2)->default(0);
            $t->decimal('discount', 14, 2)->default(0);
            $t->decimal('sgst', 14, 2)->default(0);
            $t->decimal('cgst', 14, 2)->default(0);
            $t->decimal('net_amount', 14, 2)->default(0);
            $t->string('payment_mode', 20)->default('credit');
            $t->string('status', 20)->default('active');
            $t->text('narration')->nullable();
            $t->timestamps();
        });

        Schema::create('wholesale_sales_detail', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('ws_id');
            $t->unsignedBigInteger('item_id')->nullable();
            $t->string('item_name');
            $t->string('unit', 20)->default('KG');
            $t->decimal('qty', 12, 3)->default(0);
            $t->decimal('rate', 14, 4)->default(0);
            $t->decimal('amount', 14, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('distributor_sales', function (Blueprint $t) {
            $t->id();
            $t->string('slno', 30)->nullable();
            $t->string('ds_no', 30)->nullable();
            $t->date('ds_date');
            $t->unsignedBigInteger('distributor_id')->nullable();
            $t->string('distributor_name');
            $t->string('region')->nullable();
            $t->decimal('taxable', 14, 2)->default(0);
            $t->decimal('discount', 14, 2)->default(0);
            $t->decimal('sgst', 14, 2)->default(0);
            $t->decimal('cgst', 14, 2)->default(0);
            $t->decimal('net_amount', 14, 2)->default(0);
            $t->string('payment_mode', 20)->default('credit');
            $t->string('status', 20)->default('active');
            $t->text('narration')->nullable();
            $t->timestamps();
        });

        Schema::create('distributor_sales_detail', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('ds_id');
            $t->unsignedBigInteger('item_id')->nullable();
            $t->string('item_name');
            $t->string('unit', 20)->default('KG');
            $t->decimal('qty', 12, 3)->default(0);
            $t->decimal('rate', 14, 4)->default(0);
            $t->decimal('amount', 14, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('sales_return', function (Blueprint $t) {
            $t->id();
            $t->string('slno', 30)->nullable();
            $t->string('return_no', 30)->nullable();
            $t->date('return_date');
            $t->unsignedBigInteger('invoice_id')->nullable();
            $t->unsignedBigInteger('customer_id')->nullable();
            $t->string('customer_name');
            $t->text('reason')->nullable();
            $t->decimal('taxable', 14, 2)->default(0);
            $t->decimal('sgst', 14, 2)->default(0);
            $t->decimal('cgst', 14, 2)->default(0);
            $t->decimal('net_amount', 14, 2)->default(0);
            $t->string('status', 20)->default('pending');
            $t->timestamps();
        });

        Schema::create('sales_return_detail', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('return_id');
            $t->unsignedBigInteger('item_id')->nullable();
            $t->string('item_name');
            $t->decimal('qty', 12, 3)->default(0);
            $t->decimal('rate', 14, 4)->default(0);
            $t->decimal('amount', 14, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('credit_note', function (Blueprint $t) {
            $t->id();
            $t->string('slno', 30)->nullable();
            $t->string('cn_no', 30)->nullable();
            $t->date('cn_date');
            $t->unsignedBigInteger('customer_id')->nullable();
            $t->string('customer_name');
            $t->unsignedBigInteger('ref_invoice_id')->nullable();
            $t->decimal('amount', 14, 2)->default(0);
            $t->text('reason')->nullable();
            $t->string('status', 20)->default('open');
            $t->timestamps();
        });

        Schema::create('delivery_note', function (Blueprint $t) {
            $t->id();
            $t->string('slno', 30)->nullable();
            $t->string('dn_no', 30)->nullable();
            $t->date('dn_date');
            $t->unsignedBigInteger('so_id')->nullable();
            $t->unsignedBigInteger('customer_id')->nullable();
            $t->string('customer_name');
            $t->string('delivered_by')->nullable();
            $t->unsignedBigInteger('vehicle_id')->nullable();
            $t->string('status', 20)->default('pending');
            $t->timestamps();
        });

        Schema::create('delivery_note_detail', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('dn_id');
            $t->unsignedBigInteger('item_id')->nullable();
            $t->string('item_name');
            $t->string('unit', 20)->default('KG');
            $t->decimal('ordered_qty', 12, 3)->default(0);
            $t->decimal('delivered_qty', 12, 3)->default(0);
            $t->string('batch_no')->nullable();
            $t->timestamps();
        });

        Schema::create('route_delivery', function (Blueprint $t) {
            $t->id();
            $t->string('slno', 30)->nullable();
            $t->date('delivery_date');
            $t->unsignedBigInteger('route_id')->nullable();
            $t->unsignedBigInteger('vehicle_id')->nullable();
            $t->unsignedBigInteger('driver_id')->nullable();
            $t->string('status', 20)->default('planned');
            $t->timestamps();
        });

        Schema::create('route_delivery_detail', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('route_delivery_id');
            $t->unsignedBigInteger('customer_id')->nullable();
            $t->string('customer_name');
            $t->unsignedBigInteger('dn_id')->nullable();
            $t->string('status', 20)->default('pending');
            $t->timestamps();
        });

        Schema::create('courier_shipment', function (Blueprint $t) {
            $t->id();
            $t->string('slno', 30)->nullable();
            $t->date('ship_date');
            $t->unsignedBigInteger('dn_id')->nullable();
            $t->string('courier_partner')->nullable();
            $t->string('tracking_no')->nullable();
            $t->decimal('weight', 10, 3)->default(0);
            $t->decimal('charges', 14, 2)->default(0);
            $t->string('status', 20)->default('booked');
            $t->timestamps();
        });

        Schema::create('fleet_vehicle', function (Blueprint $t) {
            $t->id();
            $t->string('vehicle_no', 30);
            $t->string('vehicle_type', 50)->nullable();
            $t->string('make_model')->nullable();
            $t->string('driver_name')->nullable();
            $t->string('driver_phone', 20)->nullable();
            $t->date('rc_expiry')->nullable();
            $t->date('insurance_expiry')->nullable();
            $t->date('fitness_expiry')->nullable();
            $t->string('status', 20)->default('active');
            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('fleet_vehicle');
        Schema::dropIfExists('courier_shipment');
        Schema::dropIfExists('route_delivery_detail');
        Schema::dropIfExists('route_delivery');
        Schema::dropIfExists('delivery_note_detail');
        Schema::dropIfExists('delivery_note');
        Schema::dropIfExists('credit_note');
        Schema::dropIfExists('sales_return_detail');
        Schema::dropIfExists('sales_return');
        Schema::dropIfExists('distributor_sales_detail');
        Schema::dropIfExists('distributor_sales');
        Schema::dropIfExists('wholesale_sales_detail');
        Schema::dropIfExists('wholesale_sales');
        Schema::dropIfExists('retail_pos_detail');
        Schema::dropIfExists('retail_pos');
        Schema::dropIfExists('sales_order_detail');
        Schema::dropIfExists('sales_order');
        Schema::dropIfExists('sales_quotation_detail');
        Schema::dropIfExists('sales_quotation');
        Schema::dropIfExists('dispatch_plan_detail');
        Schema::dropIfExists('dispatch_plan');
        Schema::dropIfExists('fg_warehouse');
    }
};
