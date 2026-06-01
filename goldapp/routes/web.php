<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
// Masters
use App\Http\Controllers\CompanyMasterController;
use App\Http\Controllers\BranchMasterController;
use App\Http\Controllers\WarehouseMasterController;
use App\Http\Controllers\KitchenFactoryMasterController;
use App\Http\Controllers\DepartmentMasterController;
use App\Http\Controllers\StaffMasterController;
use App\Http\Controllers\VendorSupplierMasterController;
use App\Http\Controllers\CustomerMasterController;
use App\Http\Controllers\DistributorMasterController;
use App\Http\Controllers\RouteMasterController;
use App\Http\Controllers\VehicleMasterController;
use App\Http\Controllers\UnitMasterController;
use App\Http\Controllers\TaxGSTMasterController;
use App\Http\Controllers\HSNSACMasterController;
use App\Http\Controllers\RawMaterialMasterController;
use App\Http\Controllers\PackingMaterialMasterController;
use App\Http\Controllers\FinishedGoodsMasterController;
use App\Http\Controllers\SemiFinishedGoodsMasterController;
use App\Http\Controllers\RecipeBOMMasterController;
use App\Http\Controllers\RecipeVersioningController;
use App\Http\Controllers\IngredientSubstitutionController;
use App\Http\Controllers\NutritionalInformationController;
use App\Http\Controllers\AllergenMasterController;
use App\Http\Controllers\FSSAIComplianceController;
// Purchase
use App\Http\Controllers\PurchaseRequisitionController;
use App\Http\Controllers\PurchaseEnquiryRFQController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\GoodsReceiptNoteController;
use App\Http\Controllers\RawMaterialQCController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\VendorPaymentController;
use App\Http\Controllers\VendorLedgerController;
use App\Http\Controllers\VendorRatingController;
use App\Http\Controllers\ContractPricingController;
// Inventory
use App\Http\Controllers\RawMaterialInventoryController;
use App\Http\Controllers\BatchLotTrackingController;
use App\Http\Controllers\ExpiryTrackingController;
use App\Http\Controllers\FIFOFEFOController;
use App\Http\Controllers\ColdStorageTrackingController;
use App\Http\Controllers\StockLedgerController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\PhysicalStockVerificationController;
use App\Http\Controllers\DamageExpiryWriteoffController;
use App\Http\Controllers\ReorderManagementController;
// Production
use App\Http\Controllers\ProductionPlanningController;
use App\Http\Controllers\ProductionOrderController;
use App\Http\Controllers\MaterialIssueToProductionController;
use App\Http\Controllers\WIPManagementController;
use App\Http\Controllers\PreparationStageController;
use App\Http\Controllers\CookingStageController;
use App\Http\Controllers\CoolingStageController;
use App\Http\Controllers\PackingStageController;
use App\Http\Controllers\FinishedGoodsReceiptController;
use App\Http\Controllers\YieldWastageCaptureController;
use App\Http\Controllers\ByproductCaptureController;
use App\Http\Controllers\ReworkManagementController;
use App\Http\Controllers\ProductionCostingController;
use App\Http\Controllers\MachineMasterController;
use App\Http\Controllers\MachineUtilizationController;
use App\Http\Controllers\DowntimeTrackingController;
use App\Http\Controllers\MaintenanceScheduleController;
// Quality
use App\Http\Controllers\QualityControlController;
use App\Http\Controllers\IncomingQCController;
use App\Http\Controllers\InprocessQCController;
use App\Http\Controllers\FinalQCController;
use App\Http\Controllers\LabTestRegisterController;
use App\Http\Controllers\SampleRetentionController;
use App\Http\Controllers\HACCPChecklistController;
use App\Http\Controllers\BatchTraceabilityController;
use App\Http\Controllers\RecallManagementController;
// Sales
use App\Http\Controllers\FinishedGoodsWarehouseController;
use App\Http\Controllers\DispatchPlanningController;
use App\Http\Controllers\SalesQuotationController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\RetailPOSController;
use App\Http\Controllers\WholesaleSalesController;
use App\Http\Controllers\DistributorSalesController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\RouteDeliveryController;
use App\Http\Controllers\CourierIntegrationController;
use App\Http\Controllers\FleetManagementController;
// E-Commerce
use App\Http\Controllers\EcommerceProductCatalogController;
use App\Http\Controllers\OnlineStoreController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OnlinePaymentController;
use App\Http\Controllers\CouponsOffersController;
use App\Http\Controllers\LoyaltyPointsController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\DistributorPortalController;
use App\Http\Controllers\OnlineReturnsController;
use App\Http\Controllers\MarketplaceIntegrationController;
use App\Http\Controllers\SwiggyZomatoAmazonShopifyWooCommerceIntegrationController;
// CRM
use App\Http\Controllers\CRMLeadsController;
use App\Http\Controllers\CRMFollowupController;
use App\Http\Controllers\CustomerComplaintsController;
use App\Http\Controllers\FeedbackController;
// Accounts
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DaybookController;
use App\Http\Controllers\JournalVoucherController;
use App\Http\Controllers\ContraVoucherController;
use App\Http\Controllers\DebitCreditNoteController;
use App\Http\Controllers\BankReconciliationController;
use App\Http\Controllers\CashBookController;
use App\Http\Controllers\BankBookController;
use App\Http\Controllers\GeneralLedgerController;
use App\Http\Controllers\ProfitAndLossController;
use App\Http\Controllers\BalanceSheetController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\COGSPostingController;
use App\Http\Controllers\GSTPurchaseRegisterController;
use App\Http\Controllers\GSTSalesRegisterController;
use App\Http\Controllers\GSTR1Controller;
use App\Http\Controllers\GSTR3BController;
use App\Http\Controllers\EinvoiceController;
use App\Http\Controllers\EwayBillController;
// HRMS
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveManagementController;
use App\Http\Controllers\ShiftManagementController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\PFESITDSController;
use App\Http\Controllers\SalaryPostingController;
// Settings
use App\Http\Controllers\AssetManagementController;
use App\Http\Controllers\BudgetingController;
use App\Http\Controllers\ApprovalWorkflowController;
use App\Http\Controllers\NotificationCenterController;
use App\Http\Controllers\SMSEmailWhatsAppLogsController;
use App\Http\Controllers\DocumentVaultController;
use App\Http\Controllers\BarcodeQRController;
use App\Http\Controllers\MobileAppAPIController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\UserRolePermissionController;
use App\Http\Controllers\AuditLogsController;
use App\Http\Controllers\BackupRestoreController;
use App\Http\Controllers\ReportBuilderController;
use App\Http\Controllers\BIDashboardController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // MASTERS (2-25)
    Route::resource('companies',                CompanyMasterController::class);
    Route::resource('branches',                 BranchMasterController::class);
    Route::resource('warehouses',               WarehouseMasterController::class);
    Route::resource('factories',                KitchenFactoryMasterController::class);
    Route::resource('departments',              DepartmentMasterController::class);
    Route::resource('staff',                    StaffMasterController::class);
    Route::resource('vendors',                  VendorSupplierMasterController::class);
    Route::resource('customers',                CustomerMasterController::class);
    Route::resource('distributors',             DistributorMasterController::class);
    Route::resource('routes',                   RouteMasterController::class);
    Route::resource('vehicles',                 VehicleMasterController::class);
    Route::resource('units',                    UnitMasterController::class);
    Route::resource('taxes',                    TaxGSTMasterController::class);
    Route::resource('hsn-sac',                  HSNSACMasterController::class);
    Route::resource('raw-materials',            RawMaterialMasterController::class);
    Route::resource('packing-materials',        PackingMaterialMasterController::class);
    Route::resource('finished-goods',           FinishedGoodsMasterController::class);
    Route::resource('semi-finished-goods',      SemiFinishedGoodsMasterController::class);
    Route::resource('recipes',                  RecipeBOMMasterController::class);
    Route::resource('recipe-versions',          RecipeVersioningController::class);
    Route::resource('ingredient-substitutions', IngredientSubstitutionController::class);
    Route::resource('nutritional-info',         NutritionalInformationController::class);
    Route::resource('allergens',                AllergenMasterController::class);
    Route::resource('fssai',                    FSSAIComplianceController::class);

    // PURCHASE (26-36)
    Route::resource('purchase-requisitions',    PurchaseRequisitionController::class);
    Route::resource('purchase-rfq',             PurchaseEnquiryRFQController::class);
    Route::resource('purchase-orders',          PurchaseOrderController::class);
    Route::resource('grn',                      GoodsReceiptNoteController::class);
    Route::resource('rm-qc',                    RawMaterialQCController::class);
    Route::resource('purchase-invoices',        PurchaseInvoiceController::class);
    Route::resource('purchase-returns',         PurchaseReturnController::class);
    Route::resource('vendor-payments',          VendorPaymentController::class);
    Route::get('vendor-ledger',                 [VendorLedgerController::class,'index'])->name('vendor-ledger.index');
    Route::resource('vendor-ratings',           VendorRatingController::class);
    Route::resource('contract-pricing',         ContractPricingController::class);

    // INVENTORY (37-47)
    Route::get('rm-inventory',    [RawMaterialInventoryController::class,'index'])->name('rm-inventory.index');
    Route::resource('batch-tracking',           BatchLotTrackingController::class);
    Route::get('expiry-tracking', [ExpiryTrackingController::class,'index'])->name('expiry-tracking.index');
    Route::get('fifo-fefo',       [FIFOFEFOController::class,'index'])->name('fifo-fefo.index');
    Route::get('cold-storage',    [ColdStorageTrackingController::class,'index'])->name('cold-storage.index');
    Route::get('stock-ledger',    [StockLedgerController::class,'index'])->name('stock-ledger.index');
    Route::resource('stock-transfer',           StockTransferController::class);
    Route::resource('stock-adjustment',         StockAdjustmentController::class);
    Route::resource('stock-verification',       PhysicalStockVerificationController::class);
    Route::resource('damage-writeoff',          DamageExpiryWriteoffController::class);
    Route::get('reorder',         [ReorderManagementController::class,'index'])->name('reorder.index');

    // PRODUCTION (48-64)
    Route::resource('production-plans',         ProductionPlanningController::class);
    Route::resource('production-orders',        ProductionOrderController::class);
    Route::resource('material-issues',          MaterialIssueToProductionController::class);
    Route::get('wip',             [WIPManagementController::class,'index'])->name('wip.index');
    Route::resource('preparation',              PreparationStageController::class);
    Route::resource('cooking',                  CookingStageController::class);
    Route::resource('cooling',                  CoolingStageController::class);
    Route::resource('packing-stage',            PackingStageController::class);
    Route::resource('fg-receipt',               FinishedGoodsReceiptController::class);
    Route::resource('yield-wastage',            YieldWastageCaptureController::class);
    Route::resource('byproduct',                ByproductCaptureController::class);
    Route::resource('rework',                   ReworkManagementController::class);
    Route::get('production-costing',[ProductionCostingController::class,'index'])->name('production-costing.index');
    Route::resource('machines',                 MachineMasterController::class);
    Route::resource('machine-utilization',      MachineUtilizationController::class);
    Route::resource('downtime',                 DowntimeTrackingController::class);
    Route::resource('maintenance',              MaintenanceScheduleController::class);

    // QUALITY (65-73)
    Route::get('quality-control', [QualityControlController::class,'index'])->name('quality-control.index');
    Route::resource('incoming-qc',              IncomingQCController::class);
    Route::resource('inprocess-qc',             InprocessQCController::class);
    Route::resource('final-qc',                 FinalQCController::class);
    Route::resource('lab-tests',                LabTestRegisterController::class);
    Route::resource('sample-retention',         SampleRetentionController::class);
    Route::resource('haccp',                    HACCPChecklistController::class);
    Route::get('batch-traceability',[BatchTraceabilityController::class,'index'])->name('batch-traceability.index');
    Route::resource('recalls',                  RecallManagementController::class);

    // SALES & DELIVERY (74-87)
    Route::get('fg-warehouse',    [FinishedGoodsWarehouseController::class,'index'])->name('fg-warehouse.index');
    Route::resource('dispatch-plans',           DispatchPlanningController::class);
    Route::resource('sales-quotations',         SalesQuotationController::class);
    Route::resource('sales-orders',             SalesOrderController::class);
    Route::resource('retail-pos',               RetailPOSController::class);
    Route::resource('wholesale-sales',          WholesaleSalesController::class);
    Route::resource('distributor-sales',        DistributorSalesController::class);
    Route::resource('sales',                    SalesInvoiceController::class);
    Route::resource('sales-returns',            SalesReturnController::class);
    Route::resource('credit-notes',             CreditNoteController::class);
    Route::resource('delivery-notes',           DeliveryNoteController::class);
    Route::resource('route-delivery',           RouteDeliveryController::class);
    Route::resource('courier',                  CourierIntegrationController::class);
    Route::resource('fleet',                    FleetManagementController::class);

    // E-COMMERCE (88-99)
    Route::resource('ecom-products',            EcommerceProductCatalogController::class);
    Route::resource('ecom-orders',              OnlineStoreController::class);
    Route::resource('cart',                     CartController::class);
    Route::resource('checkout',                 CheckoutController::class);
    Route::resource('online-payments',          OnlinePaymentController::class);
    Route::resource('coupons',                  CouponsOffersController::class);
    Route::resource('loyalty',                  LoyaltyPointsController::class);
    Route::resource('customer-portal',          CustomerPortalController::class);
    Route::resource('distributor-portal',       DistributorPortalController::class);
    Route::resource('online-returns',           OnlineReturnsController::class);
    Route::resource('marketplace',              MarketplaceIntegrationController::class);
    Route::get('integrations', [SwiggyZomatoAmazonShopifyWooCommerceIntegrationController::class,'index'])->name('integrations.index');

    // CRM (100-103)
    Route::resource('crm-leads',                CRMLeadsController::class);
    Route::resource('crm-followup',             CRMFollowupController::class);
    Route::resource('complaints',               CustomerComplaintsController::class);
    Route::resource('feedback',                 FeedbackController::class);

    // ACCOUNTS (104-124)
    Route::resource('accounts',                 AccountController::class);
    Route::resource('receipts',                 ReceiptController::class);
    Route::resource('payments',                 PaymentController::class);
    Route::resource('journal-vouchers',         JournalVoucherController::class);
    Route::resource('contra-vouchers',          ContraVoucherController::class);
    Route::resource('debit-credit-notes',       DebitCreditNoteController::class);
    Route::resource('bank-reconciliation',      BankReconciliationController::class);
    Route::get('cash-book',         [CashBookController::class,'index'])->name('cash-book.index');
    Route::get('bank-book',         [BankBookController::class,'index'])->name('bank-book.index');
    Route::get('general-ledger',    [GeneralLedgerController::class,'index'])->name('general-ledger.index');
    Route::get('daybook',           [DaybookController::class,'index'])->name('daybook.index');
    Route::get('ledger',            [DaybookController::class,'ledger'])->name('daybook.ledger');
    Route::get('trial-balance',     [DaybookController::class,'trialBalance'])->name('trial-balance.index');
    Route::get('profit-loss',       [ProfitAndLossController::class,'index'])->name('profit-loss.index');
    Route::get('balance-sheet',     [BalanceSheetController::class,'index'])->name('balance-sheet.index');
    Route::get('cash-flow',         [CashFlowController::class,'index'])->name('cash-flow.index');
    Route::get('cogs-posting',      [COGSPostingController::class,'index'])->name('cogs-posting.index');
    Route::get('gst-purchase-register',[GSTPurchaseRegisterController::class,'index'])->name('gst-purchase-register.index');
    Route::get('gst-sales-register',[GSTSalesRegisterController::class,'index'])->name('gst-sales-register.index');
    Route::get('gstr1',             [GSTR1Controller::class,'index'])->name('gstr1.index');
    Route::get('gstr3b',            [GSTR3BController::class,'index'])->name('gstr3b.index');
    Route::resource('einvoice',                 EinvoiceController::class);
    Route::resource('eway-bill',                EwayBillController::class);

    // HRMS (125-131)
    Route::resource('payroll',                  PayrollController::class);
    Route::resource('attendance',               AttendanceController::class);
    Route::resource('leaves',                   LeaveManagementController::class);
    Route::resource('shifts',                   ShiftManagementController::class);
    Route::resource('overtime',                 OvertimeController::class);
    Route::resource('pf-esi-tds',               PFESITDSController::class);
    Route::resource('salary-posting',           SalaryPostingController::class);

    // SETTINGS & ADMIN (132-145)
    Route::resource('assets',                   AssetManagementController::class);
    Route::resource('budgets',                  BudgetingController::class);
    Route::resource('approval-workflow',        ApprovalWorkflowController::class);
    Route::get('notifications', [NotificationCenterController::class,'index'])->name('notifications.index');
    Route::get('comm-logs',     [SMSEmailWhatsAppLogsController::class,'index'])->name('comm-logs.index');
    Route::resource('documents',                DocumentVaultController::class);
    Route::get('barcode',       [BarcodeQRController::class,'index'])->name('barcode.index');
    Route::get('admin-settings',[AdminSettingsController::class,'index'])->name('admin-settings.index');
    Route::post('admin-settings',[AdminSettingsController::class,'update'])->name('admin-settings.update');
    Route::resource('users',                    UserRolePermissionController::class);
    Route::get('audit-logs',    [AuditLogsController::class,'index'])->name('audit-logs.index');
    Route::get('backup',        [BackupRestoreController::class,'index'])->name('backup.index');
    Route::post('backup/create',[BackupRestoreController::class,'createBackup'])->name('backup.create');
    Route::get('report-builder',[ReportBuilderController::class,'index'])->name('report-builder.index');
    Route::get('bi-dashboard',  [BIDashboardController::class,'index'])->name('bi-dashboard.index');

    // MOBILE API (139)
    Route::prefix('api/mobile')->group(function () {
        Route::post('login',      [MobileAppAPIController::class,'login']);
        Route::get('stock/{id}',  [MobileAppAPIController::class,'stockCheck']);
        Route::get('dispatch',    [MobileAppAPIController::class,'dispatchList']);
    });
});
