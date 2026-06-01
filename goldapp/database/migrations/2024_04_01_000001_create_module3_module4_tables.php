<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // MODULE 3 ─ Finished Goods / Warehouse

        // Drop old stub tables first so we can re-create with full columns
        Schema::dropIfExists('finished_goods');
        Schema::dropIfExists('warehouse');

        Schema::create('finished_goods', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 180);
            $table->string('category', 80)->nullable();
            $table->string('sub_category', 80)->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('hsn_code', 20)->nullable();
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->decimal('mrp', 18, 2)->default(0);
            $table->decimal('selling_price', 18, 2)->default(0);
            $table->decimal('wholesale_price', 18, 2)->default(0);
            $table->decimal('distributor_price', 18, 2)->default(0);
            $table->decimal('online_price', 18, 2)->default(0);
            $table->decimal('cost_price', 18, 2)->default(0);
            $table->decimal('reorder_qty', 18, 4)->default(0);
            $table->integer('shelf_life_days')->nullable();
            $table->string('storage_temp', 50)->nullable();
            $table->string('barcode', 50)->nullable();
            $table->string('sku', 50)->nullable();
            $table->decimal('weight', 10, 3)->nullable();
            $table->string('weight_unit', 10)->nullable();
            $table->tinyInteger('is_perishable')->default(0);
            $table->json('allergen_ids')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->text('description')->nullable();
            $table->string('image_path', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('fg_stock', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fg_id');
            $table->string('fg_code', 30)->nullable();
            $table->string('fg_name', 180)->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->string('batch_no', 50);
            $table->date('mfg_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('qty_in', 18, 4)->default(0);
            $table->decimal('qty_out', 18, 4)->default(0);
            $table->decimal('qty_reserved', 18, 4)->default(0);
            $table->decimal('cost_rate', 18, 4)->default(0);
            $table->enum('status', ['available', 'expired', 'on_hold', 'dispatched'])->default('available');
            $table->timestamps();
        });

        Schema::create('fg_quality_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fg_id');
            $table->string('batch_no', 50);
            $table->date('check_date');
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->enum('result', ['pass', 'fail', 'partial'])->default('pass');
            $table->decimal('temperature', 5, 2)->nullable();
            $table->decimal('moisture_pct', 5, 2)->nullable();
            $table->text('visual_check')->nullable();
            $table->text('taste_check')->nullable();
            $table->text('micro_check')->nullable();
            $table->date('release_date')->nullable();
            $table->text('hold_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 180);
            $table->enum('type', ['raw', 'finished', 'packing', 'cold'])->default('finished');
            $table->text('address')->nullable();
            $table->string('city', 80)->nullable();
            $table->string('state', 80)->nullable();
            $table->decimal('capacity', 18, 2)->nullable();
            $table->string('capacity_unit', 20)->nullable();
            $table->decimal('temperature_min', 5, 2)->nullable();
            $table->decimal('temperature_max', 5, 2)->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('warehouse_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->string('zone', 20)->nullable();
            $table->string('rack', 20)->nullable();
            $table->string('shelf', 20)->nullable();
            $table->string('bin', 20)->nullable();
            $table->decimal('capacity', 18, 2)->nullable();
            $table->decimal('current_stock', 18, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('dispatch_orders', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique();
            $table->string('do_no', 30)->nullable();
            $table->date('do_date');
            $table->unsignedBigInteger('sales_order_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name', 180)->nullable();
            $table->text('delivery_address')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->string('driver_name', 100)->nullable();
            $table->string('driver_phone', 20)->nullable();
            $table->unsignedBigInteger('route_id')->nullable();
            $table->date('dispatch_date')->nullable();
            $table->enum('status', ['draft', 'packed', 'dispatched', 'delivered', 'returned'])->default('draft');
            $table->text('narration')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('dispatch_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('do_id');
            $table->unsignedBigInteger('fg_id')->nullable();
            $table->string('fg_code', 30)->nullable();
            $table->string('fg_name', 180)->nullable();
            $table->string('batch_no', 50)->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->decimal('qty', 18, 4)->default(0);
            $table->string('unit', 20)->nullable();
            $table->decimal('cost_rate', 18, 4)->default(0);
            $table->decimal('cost_amount', 18, 2)->default(0);
            $table->decimal('selling_rate', 18, 4)->default(0);
            $table->decimal('selling_amount', 18, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('batch_traceability', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no', 50);
            $table->unsignedBigInteger('fg_id')->nullable();
            $table->string('fg_name', 180)->nullable();
            $table->unsignedBigInteger('production_order_id')->nullable();
            $table->date('mfg_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('qty_produced', 18, 4)->default(0);
            $table->decimal('qty_dispatched', 18, 4)->default(0);
            $table->decimal('qty_returned', 18, 4)->default(0);
            $table->decimal('qty_available', 18, 4)->default(0);
            $table->enum('qc_status', ['pending', 'pass', 'fail'])->default('pending');
            $table->tinyInteger('recall_flag')->default(0);
            $table->timestamps();
        });

        // MODULE 4 ─ Sales & Orders

        Schema::create('sales_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique();
            $table->string('quot_no', 30)->nullable();
            $table->date('quot_date');
            $table->date('valid_till')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name', 180)->nullable();
            $table->text('customer_address')->nullable();
            $table->enum('channel', ['retail', 'wholesale', 'distributor', 'online', 'export'])->default('retail');
            $table->decimal('taxable_amount', 18, 2)->default(0);
            $table->decimal('discount', 18, 2)->default(0);
            $table->decimal('sgst', 18, 2)->default(0);
            $table->decimal('cgst', 18, 2)->default(0);
            $table->decimal('igst', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired'])->default('draft');
            $table->text('terms')->nullable();
            $table->text('narration')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_quotation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quot_id');
            $table->unsignedBigInteger('fg_id')->nullable();
            $table->string('fg_code', 30)->nullable();
            $table->string('fg_name', 180)->nullable();
            $table->string('hsn_code', 20)->nullable();
            $table->string('unit', 20)->nullable();
            $table->decimal('qty', 18, 4)->default(0);
            $table->decimal('rate', 18, 4)->default(0);
            $table->decimal('amount', 18, 2)->default(0);
            $table->decimal('discount_pct', 6, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('sgst_pct', 6, 2)->default(0);
            $table->decimal('cgst_pct', 6, 2)->default(0);
            $table->decimal('igst_pct', 6, 2)->default(0);
            $table->decimal('sgst', 18, 2)->default(0);
            $table->decimal('cgst', 18, 2)->default(0);
            $table->decimal('igst', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique();
            $table->string('so_no', 30)->nullable();
            $table->date('so_date');
            $table->unsignedBigInteger('quot_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name', 180)->nullable();
            $table->text('delivery_address')->nullable();
            $table->date('delivery_date')->nullable();
            $table->enum('channel', ['retail', 'wholesale', 'distributor', 'online', 'export'])->default('retail');
            $table->string('payment_terms', 100)->nullable();
            $table->decimal('taxable_amount', 18, 2)->default(0);
            $table->decimal('discount', 18, 2)->default(0);
            $table->decimal('sgst', 18, 2)->default(0);
            $table->decimal('cgst', 18, 2)->default(0);
            $table->decimal('igst', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->decimal('advance_received', 18, 2)->default(0);
            $table->decimal('balance_amount', 18, 2)->default(0);
            $table->enum('status', ['open', 'confirmed', 'partially_dispatched', 'dispatched', 'completed', 'cancelled'])->default('open');
            $table->text('narration')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('so_id');
            $table->unsignedBigInteger('fg_id')->nullable();
            $table->string('fg_code', 30)->nullable();
            $table->string('fg_name', 180)->nullable();
            $table->string('hsn_code', 20)->nullable();
            $table->string('unit', 20)->nullable();
            $table->decimal('ordered_qty', 18, 4)->default(0);
            $table->decimal('dispatched_qty', 18, 4)->default(0);
            $table->decimal('pending_qty', 18, 4)->default(0);
            $table->decimal('rate', 18, 4)->default(0);
            $table->decimal('amount', 18, 2)->default(0);
            $table->decimal('discount_pct', 6, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('sgst', 18, 2)->default(0);
            $table->decimal('cgst', 18, 2)->default(0);
            $table->decimal('igst', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->timestamps();
        });

        // Drop old stub and re-create with full columns
        Schema::dropIfExists('sales_invoice');
        Schema::dropIfExists('sales_invoice_detail');

        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique();
            $table->string('invoice_no', 30)->nullable();
            $table->date('invoice_date');
            $table->unsignedBigInteger('so_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name', 180)->nullable();
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->enum('channel', ['retail', 'wholesale', 'distributor', 'online', 'export'])->default('retail');
            $table->decimal('taxable_amount', 18, 2)->default(0);
            $table->decimal('discount', 18, 2)->default(0);
            $table->decimal('sgst', 18, 2)->default(0);
            $table->decimal('cgst', 18, 2)->default(0);
            $table->decimal('igst', 18, 2)->default(0);
            $table->decimal('tcs', 18, 2)->default(0);
            $table->decimal('other_charges', 18, 2)->default(0);
            $table->decimal('round_off', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->decimal('received_amount', 18, 2)->default(0);
            $table->decimal('balance_amount', 18, 2)->default(0);
            $table->enum('payment_mode', ['cash', 'bank', 'cheque', 'credit', 'upi'])->default('credit');
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->string('cheque_no', 50)->nullable();
            $table->string('utr_no', 50)->nullable();
            $table->string('irn_no', 100)->nullable();
            $table->string('eway_bill_no', 50)->nullable();
            $table->enum('status', ['draft', 'posted', 'paid', 'partial', 'cancelled'])->default('draft');
            $table->text('narration')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('fg_id')->nullable();
            $table->string('fg_code', 30)->nullable();
            $table->string('fg_name', 180)->nullable();
            $table->string('hsn_code', 20)->nullable();
            $table->string('unit', 20)->nullable();
            $table->decimal('qty', 18, 4)->default(0);
            $table->decimal('rate', 18, 4)->default(0);
            $table->decimal('amount', 18, 2)->default(0);
            $table->decimal('discount_pct', 6, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('sgst_pct', 6, 2)->default(0);
            $table->decimal('cgst_pct', 6, 2)->default(0);
            $table->decimal('igst_pct', 6, 2)->default(0);
            $table->decimal('sgst', 18, 2)->default(0);
            $table->decimal('cgst', 18, 2)->default(0);
            $table->decimal('igst', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->string('batch_no', 50)->nullable();
            $table->decimal('cost_rate', 18, 4)->default(0);
            $table->timestamps();
        });

        // Drop old stub
        Schema::dropIfExists('sales_return');

        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique();
            $table->string('return_no', 30)->nullable();
            $table->date('return_date');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('so_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name', 180)->nullable();
            $table->string('channel', 20)->nullable();
            $table->text('reason')->nullable();
            $table->decimal('taxable_amount', 18, 2)->default(0);
            $table->decimal('sgst', 18, 2)->default(0);
            $table->decimal('cgst', 18, 2)->default(0);
            $table->decimal('igst', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->enum('refund_mode', ['credit_note', 'cash_refund', 'bank_refund'])->default('credit_note');
            $table->enum('status', ['draft', 'approved', 'completed'])->default('draft');
            $table->text('narration')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_id');
            $table->unsignedBigInteger('fg_id')->nullable();
            $table->string('fg_code', 30)->nullable();
            $table->string('fg_name', 180)->nullable();
            $table->string('unit', 20)->nullable();
            $table->decimal('qty', 18, 4)->default(0);
            $table->decimal('rate', 18, 4)->default(0);
            $table->decimal('amount', 18, 2)->default(0);
            $table->string('batch_no', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('ar_ledger', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->date('tdate');
            $table->string('slno', 30)->nullable();
            $table->string('vtype', 20)->nullable();
            $table->string('ref_no', 50)->nullable();
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);
            $table->decimal('balance', 18, 2)->default(0);
            $table->text('narration')->nullable();
            $table->timestamps();
        });

        Schema::create('pos_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_no', 30)->nullable();
            $table->unsignedBigInteger('cashier_id')->nullable();
            $table->string('terminal', 50)->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->decimal('opening_cash', 18, 2)->default(0);
            $table->decimal('closing_cash', 18, 2)->nullable();
            $table->decimal('total_sales', 18, 2)->default(0);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });

        Schema::create('pos_bills', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique();
            $table->string('bill_no', 30)->nullable();
            $table->date('bill_date');
            $table->unsignedBigInteger('session_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name', 180)->nullable();
            $table->decimal('taxable_amount', 18, 2)->default(0);
            $table->decimal('discount', 18, 2)->default(0);
            $table->decimal('sgst', 18, 2)->default(0);
            $table->decimal('cgst', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->decimal('received_amount', 18, 2)->default(0);
            $table->decimal('change_amount', 18, 2)->default(0);
            $table->enum('payment_mode', ['cash', 'card', 'upi', 'split'])->default('cash');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('cashier_id')->nullable();
            $table->timestamps();
        });

        Schema::create('pos_bill_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->unsignedBigInteger('fg_id')->nullable();
            $table->string('fg_code', 30)->nullable();
            $table->string('fg_name', 180)->nullable();
            $table->decimal('qty', 18, 4)->default(0);
            $table->decimal('rate', 18, 4)->default(0);
            $table->decimal('discount_pct', 6, 2)->default(0);
            $table->decimal('amount', 18, 2)->default(0);
            $table->decimal('sgst', 18, 2)->default(0);
            $table->decimal('cgst', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->string('batch_no', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_bill_items');
        Schema::dropIfExists('pos_bills');
        Schema::dropIfExists('pos_sessions');
        Schema::dropIfExists('ar_ledger');
        Schema::dropIfExists('sales_return_items');
        Schema::dropIfExists('sales_returns');
        Schema::dropIfExists('sales_invoice_items');
        Schema::dropIfExists('sales_invoices');
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_orders');
        Schema::dropIfExists('sales_quotation_items');
        Schema::dropIfExists('sales_quotations');
        Schema::dropIfExists('batch_traceability');
        Schema::dropIfExists('dispatch_order_items');
        Schema::dropIfExists('dispatch_orders');
        Schema::dropIfExists('warehouse_locations');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('fg_quality_checks');
        Schema::dropIfExists('fg_stock');
        Schema::dropIfExists('finished_goods');
    }
};
