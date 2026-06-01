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
// New module controllers
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\NewPurchaseOrderController;
use App\Http\Controllers\GRNController;
use App\Http\Controllers\NewPurchaseInvoiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CRMLeadController;
use App\Http\Controllers\CRMActivityController;
use App\Http\Controllers\CRMOpportunityController;
use App\Http\Controllers\CustomerComplaintController;
use App\Http\Controllers\FinishedGoodsController;
use App\Http\Controllers\FGStockController;
use App\Http\Controllers\FGQualityCheckController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\DispatchOrderController;
use App\Http\Controllers\PLReportController;
use App\Http\Controllers\SalesAnalyticsController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\AgeingReportController;
use App\Http\Controllers\ForecastingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\APIClientController;
use App\Http\Controllers\IntegrationSettingsController;
use App\Http\Controllers\GSTFilingController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\EcomChannelController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\MobileAPIController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\FinancialYearController;
use App\Http\Controllers\SequenceConfigController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\SMSWhatsAppTemplateController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\NotificationPreferenceController;
use App\Http\Controllers\AdminDashboardController;

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

    // NEW MODULES (1-10 detailed controllers)
    Route::resource('suppliers',                   SupplierController::class);
    Route::resource('new-purchase-orders',         NewPurchaseOrderController::class);
    Route::resource('new-grn',                     GRNController::class);
    Route::resource('new-purchase-invoices',       NewPurchaseInvoiceController::class);
    Route::resource('crm-customers',               CustomerController::class);
    Route::resource('new-crm-leads',               CRMLeadController::class);
    Route::resource('crm-activities',              CRMActivityController::class);
    Route::resource('crm-opportunities',           CRMOpportunityController::class);
    Route::resource('customer-complaints',         CustomerComplaintController::class);
    Route::resource('fg-products',                 FinishedGoodsController::class);
    Route::resource('fg-stock',                    FGStockController::class);
    Route::resource('fg-quality',                  FGQualityCheckController::class);
    Route::resource('warehouse-mgmt',              WarehouseController::class);
    Route::resource('dispatch-orders',             DispatchOrderController::class);
    Route::get('pl-report',            [PLReportController::class,'index'])->name('pl-report.index');
    Route::get('sales-analytics',      [SalesAnalyticsController::class,'index'])->name('sales-analytics.index');
    Route::get('inventory-report',     [InventoryReportController::class,'index'])->name('inventory-report.index');
    Route::get('ageing-report',        [AgeingReportController::class,'index'])->name('ageing-report.index');

    // Module 7: Analytics Reports (canonical URLs)
    Route::get('reports/profit-loss',   [PLReportController::class,'index'])->name('reports.pl');
    Route::get('reports/sales-analytics',[SalesAnalyticsController::class,'index'])->name('reports.sales');
    Route::get('reports/inventory',     [InventoryReportController::class,'index'])->name('reports.inventory');
    Route::get('reports/ageing',        [AgeingReportController::class,'index'])->name('reports.ageing');
    Route::get('reports/forecasting',   [ForecastingController::class,'index'])->name('reports.forecast');

    // Module 8: User & Role Management
    Route::resource('admin/users',      UserController::class)->names([
        'index'   => 'admin.users.index',
        'create'  => 'admin.users.create',
        'store'   => 'admin.users.store',
        'show'    => 'admin.users.show',
        'edit'    => 'admin.users.edit',
        'update'  => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
    Route::post('admin/users/{id}/reset-password', [UserController::class,'resetPassword'])->name('users.reset-password');
    Route::post('admin/users/{id}/impersonate',    [UserController::class,'impersonate'])->name('users.impersonate');
    Route::resource('admin/roles',      RoleController::class)->names([
        'index'   => 'admin.roles.index',
        'create'  => 'admin.roles.create',
        'store'   => 'admin.roles.store',
        'show'    => 'admin.roles.show',
        'edit'    => 'admin.roles.edit',
        'update'  => 'admin.roles.update',
        'destroy' => 'admin.roles.destroy',
    ]);
    Route::get('admin/roles/{id}/permissions',    [RoleController::class,'permissions'])->name('roles.permissions');
    Route::post('admin/roles/{id}/permissions',   [RoleController::class,'savePermissions'])->name('roles.save-permissions');
    Route::resource('admin/permissions',PermissionController::class)->names([
        'index'   => 'admin.permissions.index',
        'create'  => 'admin.permissions.create',
        'store'   => 'admin.permissions.store',
        'show'    => 'admin.permissions.show',
        'edit'    => 'admin.permissions.edit',
        'update'  => 'admin.permissions.update',
        'destroy' => 'admin.permissions.destroy',
    ]);
    Route::post('admin/permissions/auto-generate',[PermissionController::class,'autoGenerate'])->name('permissions.auto-generate');
    Route::get('admin/audit-logs',      [AuditLogController::class,'index'])->name('admin.audit-logs.index');
    Route::get('admin/audit-logs/{id}', [AuditLogController::class,'show'])->name('admin.audit-logs.show');
    Route::get('admin/profile',         [ProfileController::class,'edit'])->name('profile.edit');
    Route::put('admin/profile',         [ProfileController::class,'update'])->name('profile.update');
    Route::resource('api-clients',                 APIClientController::class);
    Route::get('api-clients/{apiClient}/logs', [APIClientController::class, 'logs'])->name('api-clients.logs');

    // MOBILE API (139)
    Route::prefix('api/mobile')->group(function () {
        Route::post('login',      [MobileAppAPIController::class,'login']);
        Route::get('stock/{id}',  [MobileAppAPIController::class,'stockCheck']);
        Route::get('dispatch',    [MobileAppAPIController::class,'dispatchList']);
    });

    // ── Module 9: Integrations & API ──────────────────────────────────────────
    Route::get('integrations', [IntegrationSettingsController::class, 'index'])->name('integrations.index');
    Route::get('integrations/configure/{type}', [IntegrationSettingsController::class, 'configure'])->name('integrations.configure');
    Route::post('integrations/configure/{type}', [IntegrationSettingsController::class, 'save'])->name('integrations.save');
    Route::post('integrations/test/{type}', [IntegrationSettingsController::class, 'test'])->name('integrations.test');
    Route::post('integrations/sync/{type}', [IntegrationSettingsController::class, 'sync'])->name('integrations.sync');

    Route::resource('gst-filings', GSTFilingController::class);
    Route::post('gst-filings/generate', [GSTFilingController::class, 'generate'])->name('gst-filings.generate');
    Route::get('gst-filings/{gstFiling}/download', [GSTFilingController::class, 'download'])->name('gst-filings.download');

    Route::resource('einvoices', EinvoiceController::class);
    Route::post('einvoices/{id}/generate', [EinvoiceController::class, 'generate'])->name('einvoices.generate');
    Route::post('einvoices/{id}/cancel', [EinvoiceController::class, 'cancelIrn'])->name('einvoices.cancel');

    Route::resource('eway-bills', EwayBillController::class);

    Route::get('payment-gateway', [PaymentGatewayController::class, 'index'])->name('payment-gateway.index');
    Route::post('payment-gateway/{id}/reconcile', [PaymentGatewayController::class, 'reconcile'])->name('payment-gateway.reconcile');

    Route::resource('couriers', CourierController::class);

    Route::get('ecom-orders', [EcomChannelController::class, 'index'])->name('ecom-orders.index');
    Route::post('ecom-orders/{ecomChannelOrder}/map-so', [EcomChannelController::class, 'mapToSO'])->name('ecom-orders.map-so');
    Route::post('ecom-orders/{ecomChannelOrder}/process', [EcomChannelController::class, 'process'])->name('ecom-orders.process');

    // ── Module 10: Settings & Admin ───────────────────────────────────────────
    Route::get('company-profile', [CompanyProfileController::class, 'index'])->name('company-profile.index');
    Route::put('company-profile', [CompanyProfileController::class, 'update'])->name('company-profile.update');

    Route::resource('financial-years', FinancialYearController::class);
    Route::post('financial-years/{id}/lock', [FinancialYearController::class, 'lock'])->name('financial-years.lock');
    Route::post('financial-years/{id}/set-current', [FinancialYearController::class, 'setCurrent'])->name('financial-years.set-current');

    Route::resource('sequences', SequenceConfigController::class);
    Route::get('sequences/{sequence}/preview', [SequenceConfigController::class, 'preview'])->name('sequences.preview');
    Route::post('sequences/{sequence}/reset', [SequenceConfigController::class, 'reset'])->name('sequences.reset');

    Route::resource('email-templates', EmailTemplateController::class);
    Route::get('email-templates/{emailTemplate}/preview', [EmailTemplateController::class, 'preview'])->name('email-templates.preview');

    Route::resource('sms-templates', SMSWhatsAppTemplateController::class)->parameters(['sms-templates' => 'smsTemplate']);

    Route::get('system-settings', [SystemSettingsController::class, 'index'])->name('system-settings.index');
    Route::post('system-settings', [SystemSettingsController::class, 'update'])->name('system-settings.update');

    Route::get('backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('backup/create', [BackupController::class, 'create'])->name('backup.create');
    Route::get('backup/{id}/download', [BackupController::class, 'download'])->name('backup.download');
    Route::post('backup/{id}/restore', [BackupController::class, 'restore'])->name('backup.restore');

    Route::get('notification-prefs', [NotificationPreferenceController::class, 'index'])->name('notification-prefs.index');
    Route::post('notification-prefs', [NotificationPreferenceController::class, 'update'])->name('notification-prefs.update');

    Route::get('admin-dashboard', [AdminDashboardController::class, 'index'])->name('admin-dashboard.index');

    // ============================================================
    // MODULE 1: PROCUREMENT (Full Implementation)
    // ============================================================
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);

    Route::resource('purchase-orders', \App\Http\Controllers\NewPurchaseOrderController::class)
        ->names(['index'=>'purchase-orders.index','create'=>'purchase-orders.create','store'=>'purchase-orders.store','show'=>'purchase-orders.show','edit'=>'purchase-orders.edit','update'=>'purchase-orders.update','destroy'=>'purchase-orders.destroy']);
    Route::post('purchase-orders/{id}/approve', [\App\Http\Controllers\NewPurchaseOrderController::class, 'approve'])
        ->name('purchase-orders.approve');

    Route::resource('grn', \App\Http\Controllers\GRNController::class)
        ->names(['index'=>'grn.index','create'=>'grn.create','store'=>'grn.store','show'=>'grn.show','edit'=>'grn.edit','update'=>'grn.update','destroy'=>'grn.destroy']);

    Route::resource('purchase-invoices-new', \App\Http\Controllers\NewPurchaseInvoiceController::class)
        ->names(['index'=>'purchase-invoices-new.index','create'=>'purchase-invoices-new.create','store'=>'purchase-invoices-new.store','show'=>'purchase-invoices-new.show','edit'=>'purchase-invoices-new.edit','update'=>'purchase-invoices-new.update','destroy'=>'purchase-invoices-new.destroy']);

    Route::resource('purchase-returns-new', \App\Http\Controllers\NewPurchaseReturnController::class)
        ->names(['index'=>'purchase-returns-new.index','create'=>'purchase-returns-new.create','store'=>'purchase-returns-new.store','show'=>'purchase-returns-new.show','edit'=>'purchase-returns-new.edit','update'=>'purchase-returns-new.update','destroy'=>'purchase-returns-new.destroy']);
    Route::post('purchase-returns-new/{id}/approve', [\App\Http\Controllers\NewPurchaseReturnController::class, 'approve'])
        ->name('purchase-returns-new.approve');

    Route::get('ap-ledger', [\App\Http\Controllers\APLedgerController::class, 'index'])->name('ap-ledger.index');

    // ============================================================
    // MODULE 2: INVENTORY & PRODUCTION (Full Implementation)
    // ============================================================
    Route::resource('raw-materials-new', \App\Http\Controllers\NewRawMaterialController::class)
        ->names(['index'=>'raw-materials-new.index','create'=>'raw-materials-new.create','store'=>'raw-materials-new.store','show'=>'raw-materials-new.show','edit'=>'raw-materials-new.edit','update'=>'raw-materials-new.update','destroy'=>'raw-materials-new.destroy']);

    Route::get('inventory-stock-new', [\App\Http\Controllers\NewInventoryStockController::class, 'index'])
        ->name('inventory-stock-new.index');

    Route::get('stock-movements-new', [\App\Http\Controllers\NewStockMovementController::class, 'index'])
        ->name('stock-movements-new.index');

    Route::resource('production-orders-new', \App\Http\Controllers\NewProductionOrderController::class)
        ->names(['index'=>'production-orders-new.index','create'=>'production-orders-new.create','store'=>'production-orders-new.store','show'=>'production-orders-new.show','edit'=>'production-orders-new.edit','update'=>'production-orders-new.update','destroy'=>'production-orders-new.destroy']);
    Route::get('production-orders-new/{id}/issue-form', [\App\Http\Controllers\NewProductionOrderController::class, 'issueForm'])
        ->name('production-orders-new.issueForm');
    Route::post('production-orders-new/{id}/issue', [\App\Http\Controllers\NewProductionOrderController::class, 'issue'])
        ->name('production-orders-new.issue');
    Route::post('production-orders-new/{id}/complete', [\App\Http\Controllers\NewProductionOrderController::class, 'complete'])
        ->name('production-orders-new.complete');

    Route::get('wip-new', [\App\Http\Controllers\NewWIPController::class, 'index'])->name('wip-new.index');

    Route::get('production-costing-new', [\App\Http\Controllers\NewProductionCostingController::class, 'index'])
        ->name('production-costing-new.index');
});

// Webhooks (no auth)
Route::post('webhooks/{source}', [WebhookController::class, 'receive'])->name('webhooks.receive');

// Mobile API v1 (token auth via Sanctum or session)
Route::prefix('api/v1')->group(function () {
    Route::post('auth/login', [MobileAPIController::class, 'login']);
    Route::middleware('auth')->group(function () {
        Route::get('dashboard', [MobileAPIController::class, 'dashboard']);
        Route::get('stock/{item_id}', [MobileAPIController::class, 'stockCheck']);
        Route::get('sales-orders', [MobileAPIController::class, 'salesOrders']);
        Route::post('dispatch/{id}/confirm', [MobileAPIController::class, 'confirmDispatch']);
    });
});

// ============================================================
// MODULE 3 & 4: Finished Goods / Warehouse + Sales & Orders
// ============================================================
Route::middleware('auth')->group(function () {

    // Finished Goods CRUD
    Route::resource('finished-goods', \App\Http\Controllers\FinishedGoodsController::class);

    // FG Stock (read-only index)
    Route::get('fg-stock', [\App\Http\Controllers\FGStockController::class, 'index'])->name('fg-stock.index');

    // FG Quality Checks
    Route::resource('fg-quality', \App\Http\Controllers\FGQualityCheckController::class);

    // Warehouses (new full controller, different resource name to avoid collision)
    Route::resource('warehouses-full', \App\Http\Controllers\WarehouseController::class)
        ->parameters(['warehouses-full' => 'warehouse'])
        ->names([
            'index'   => 'warehouses.index',
            'create'  => 'warehouses.create',
            'store'   => 'warehouses.store',
            'show'    => 'warehouses.show',
            'edit'    => 'warehouses.edit',
            'update'  => 'warehouses.update',
            'destroy' => 'warehouses.destroy',
        ]);

    // Dispatch Orders
    Route::resource('dispatch-orders', \App\Http\Controllers\DispatchOrderController::class);
    Route::post('dispatch-orders/{id}/dispatch', [\App\Http\Controllers\DispatchOrderController::class, 'dispatch'])
        ->name('dispatch-orders.dispatch');
    Route::post('dispatch-orders/{id}/deliver', [\App\Http\Controllers\DispatchOrderController::class, 'deliver'])
        ->name('dispatch-orders.deliver');

    // Batch Traceability
    Route::get('batch-trace', [\App\Http\Controllers\BatchTraceabilityController::class, 'index'])->name('batch-trace.index');
    Route::get('batch-trace/{batchNo}', [\App\Http\Controllers\BatchTraceabilityController::class, 'show'])->name('batch-trace.show');

    // Sales Quotations
    Route::resource('sales-quotations', \App\Http\Controllers\SalesQuotationController::class);
    Route::post('sales-quotations/{salesQuotation}/convert-so', [\App\Http\Controllers\SalesQuotationController::class, 'convertToSO'])
        ->name('sales-quotations.convert-so');

    // Sales Orders
    Route::resource('sales-orders', \App\Http\Controllers\SalesOrderController::class);
    Route::post('sales-orders/{salesOrder}/confirm', [\App\Http\Controllers\SalesOrderController::class, 'confirm'])
        ->name('sales-orders.confirm');

    // Sales Invoices (full double-entry)
    Route::resource('sales-invoices', \App\Http\Controllers\SalesInvoiceFullController::class)
        ->parameters(['sales-invoices' => 'salesInvoice']);

    // Sales Returns
    Route::resource('sales-returns', \App\Http\Controllers\SalesReturnController::class);
    Route::post('sales-returns/{salesReturn}/approve', [\App\Http\Controllers\SalesReturnController::class, 'approve'])
        ->name('sales-returns.approve');

    // AR Ledger / Ageing
    Route::get('ar-ledger', [\App\Http\Controllers\ARLedgerController::class, 'index'])->name('ar-ledger.index');

    // POS
    Route::resource('pos', \App\Http\Controllers\POSController::class);
    Route::get('pos/{posSession}/bill', [\App\Http\Controllers\POSController::class, 'bill'])->name('pos.bill');
    Route::post('pos/{posSession}/save-bill', [\App\Http\Controllers\POSController::class, 'saveBill'])->name('pos.save-bill');
    Route::post('pos/{posSession}/close', [\App\Http\Controllers\POSController::class, 'closeSession'])->name('pos.close');
});
