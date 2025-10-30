<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreditOrDebitController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\BatchCourseController;
use App\Http\Controllers\ClientProjectController;
use App\Http\Controllers\CompanyOwnProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InternshipController;
use App\Http\Controllers\LoanAndInstallmentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RevenueAndTargetController;
use App\Http\Controllers\OfflineCostController;
use App\Http\Controllers\OnlineCostController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StaffSalaryController;
use App\Http\Controllers\StudentController;

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/yearlyreports', [DashboardController::class, 'index']
    )->name('yearlyreports');
    Route::post('/yearlysummary/pdf', [DashboardController::class, 'downloadPDF']
    )->name('yearlysummary.downloadpdf');



    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])
    ->name('notifications.index');



    // Credit and Debit Routes

        // Summary Credit or Debit
        Route::get('/credit-debit/summary', [CreditOrDebitController::class, 'index']
        )->name('credit.debit.summary');
        // Create Credit or Debit Transaction
        Route::get('/credit-debit/transaction', [CreditOrDebitController::class, 'showTransactionForm']
        )->name('credit.debit.transaction');
        // Store Credit or Debit Transaction
        Route::post('/credit-debit/transaction', [CreditOrDebitController::class, 'store']
        )->name('credit.debit.transaction.store');
        // Report generation for Credit or Debit
        Route::get('/credit-debit/report', [CreditOrDebitController::class, 'report']
        )->name('credit.debit.report');
        // Download PDF for Credit or Debit
        Route::post('/credit-debit/report/pdf', [CreditOrDebitController::class, 'downloadPDF']
        )->name('credit.debit.download.pdf');
        Route::get('/credit-debit/{id}/edit', [CreditOrDebitController::class, 'edit']
        )->name('credit.debit.transaction.edit');
        Route::put('/credit-debit/{id}/update', [CreditOrDebitController::class, 'update']
        )->name('credit.debit.transaction.update');
        Route::delete('/credit-debit/{id}/delete', [CreditOrDebitController::class, 'destroy']
        )->name('credit.debit.transaction.destroy');
        Route::post('/credit-debit/pdf', [CreditOrDebitController::class, 'downloadPendingPDF']
        )->name('credit.debit.pending.download.pdf');
        Route::post('/credit-debit/status-update', [CreditOrDebitController::class, 'updateStatus']
        )->name('credit.debit.status.update');




    // Student/Internship Routes

        // Batch and Course View
        Route::get('/student-internship/batch-course', [BatchCourseController::class, 'index']
        )->name('batch.course');   
        // Batch Creation
        Route::post('/student-internship/batch', [BatchCourseController::class, 'createBatch']
        )->name('batch.create');
        // Course Creation
        Route::post('/student-internship/course', [BatchCourseController::class, 'createCourse']
        )->name('course.create');
        // Batch Update and Delete
        Route::post('/batch/update/{id}', [BatchCourseController::class, 'updateBatch']
        )->name('batch.update');
        Route::delete('/batch/delete/{id}', [BatchCourseController::class, 'destroyBatch']
        )->name('batch.delete');
        // Course Update and Delete
        Route::post('/course/update/{id}', [BatchCourseController::class, 'updateCourse']
        )->name('course.update');
        Route::delete('/course/delete/{id}', [BatchCourseController::class, 'destroyCourse']
        )->name('course.delete');
        // Download Batch and Course PDF
        Route::get('/batch/download-pdf', [BatchCourseController::class, 'downloadBatchPdf']
        )->name('batch.download.pdf');
        Route::get('/course/download-pdf', [BatchCourseController::class, 'downloadCoursePdf']
        )->name('course.download.pdf');


        // Student/Internship summary
        Route::get('/student-internship/summary', [StudentController::class, 'summary']
        )->name('student.internship.summary');


        // Student Payment
        Route::get('/student/payment', [StudentController::class, 'payment']
        )->name('student.payment');
        // Store Student Payment
        Route::post('/student/payment/store', [StudentController::class, 'paymentStore']
        )->name('student.payment.store');
        // Student Registration
        Route::get('/student-registration/new', [StudentController::class, 'newRegistration']
        )->name('student.registration');
        // Student Registration Data Store
        Route::post('/student-registration/store', [StudentController::class, 'store']
        )->name('student.registration.store');
        // Student Individual Details
        Route::get('/student/individual/{id}', [StudentController::class, 'studentDetails']
        )->name('student.individual');
        // Student Individual Edit
        Route::get('/student/edit/{id}', [StudentController::class, 'editStudent']
        )->name('student.edit');
        // Student Individual Update
        Route::put('/student/update/{student}', [StudentController::class, 'updateStudent']
        )->name('student.update');
        // Student Delete
        Route::delete('/student/delete/{id}', [StudentController::class, 'destroyStudent']
        )->name('student.delete');
        // Student data expport pdf
        Route::get('/students/{id}/download-pdf', [StudentController::class, 'downloadPdf']
        )->name('student.download.pdf');
        // Students list page
        Route::get('/students', [StudentController::class, 'list']
        )->name('students.list');
        // Update statuses (POST)
        Route::post('/students/{id}/update-payment', [StudentController::class, 'updatePaymentStatus']
        )->name('student.payment.update');
        Route::post('/students/{id}/update-active', [StudentController::class, 'updateActiveStatus']
        )->name('student.active.status.update');
        Route::post('/students/{id}/update-approval', [StudentController::class, 'updateApprovalStatus']
        )->name('student.approve.status.update');
        // Download filtered list PDF
        Route::get('/students/download/pdf', [StudentController::class, 'downloadListPdf']
        )->name('students.list.download.pdf');


        // Internship Registration 
        Route::get('/internship-registration/new', [InternshipController::class, 'newRegistration']
        )->name('internship.registration');
        // Internship Registration data Store
        Route::post('/internship-registration/store', [InternshipController::class, 'store']
        )->name('internship.registration.store');
        // Internship Payment Update
        Route::post('/internship/payment/update/{id}', [InternshipController::class, 'paymentUpdate']
        )->name('internship.payment.update');
        // Internship Individual Details
        Route::get('/internship/individual/{id}', [InternshipController::class, 'studentDetails']
        )->name('internship.individual');
        // Internship Individual Edit
        Route::get('/internship/edit/{id}', [InternshipController::class, 'editStudent']
        )->name('internship.edit');
        // Internship Individual Update
        Route::put('/internship/update/{student}', [InternshipController::class, 'update']
        )->name('internship.update');
        // Internship Delete
        Route::delete('/internship/delete/{id}', [InternshipController::class, 'destroyStudent']
        )->name('internship.delete');
        // Internship data export pdf
        Route::get('/internship/{id}/download-pdf', [InternshipController::class, 'downloadPdf']
        )->name('internship.download.pdf');
        // Internship list page
        Route::get('/internship', [InternshipController::class, 'list']
        )->name('internship.list');
        // Update statuses (POST)
        Route::post('/internship/{id}/update-active', [InternshipController::class, 'updateActiveStatus']
        )->name('internship.active.status.update');
        Route::post('/internship/{id}/update-approval', [InternshipController::class, 'updateApprovalStatus']
        )->name('internship.approve.status.update');
        // Download filtered list PDF
        Route::get('/internship/download/pdf', [InternshipController::class, 'downloadListPdf']
        )->name('internship.list.download.pdf');



    // Staff salary Management    
    
        // Staff Summary
        Route::get('/staff/summary', [StaffSalaryController::class, 'index']
        )->name('staff.summary');
        // Staff Creation
        Route::get('/staff/create', [StaffSalaryController::class, 'createStaff']
        )->name('staff.create');
        // Staff Category Creation
        Route::post('/staff/category', [StaffSalaryController::class, 'createStaffCategory']
        )->name('staff.category.create');
        // Staff store
        Route::post('/staff/store', [StaffSalaryController::class, 'storeStaff']
        )->name('staff.store');
        // Staff Salary List
        Route::get('/staff/salary/list', [StaffSalaryController::class, 'staffSalaryList']
        )->name('staff.salary.list');
        // Staff Salary Payment
        Route::post('/staff/salaries/pay/{id}', [StaffSalaryController::class, 'paySalary']
        )->name('staff.salaries.pay');
        // Staff Salary Mark Paid
        Route::post('/staff/salaries/mark-paid/{id}', [StaffSalaryController::class, 'markPaid']
        )->name('staff.salaries.markPaid');
        // Staff Salary Report
        Route::get('/staff/salary/report', [StaffSalaryController::class, 'staffSalaryReport']
        )->name('staff.report');
        // Staff Salary Report by type
        Route::get('/staff/salary/report/{type}', [StaffSalaryController::class, 'staffSalaryReport']
        )->name('staff.salary.report');
        // Staff Salary Update
        Route::put('/staff/salary/update/{id}', [StaffSalaryController::class, 'updateStaffSalary']
        )->name('staff.salary.update');
    


    // Office Offline Cost Management

    // Offline Cost Category
    Route::prefix('offline/category')->group(function () {
        Route::get('/', [OfflineCostController::class, 'categoryCreate'])->name('offline.category');
        Route::post('/create', [OfflineCostController::class, 'categoryStore'])->name('offline.category.create');
        Route::post('/update/{id}', [OfflineCostController::class, 'updatecategory'])->name('offline.category.update');
        Route::delete('/delete/{id}', [OfflineCostController::class, 'destroycategory'])->name('offline.category.delete');
        Route::get('/download/pdf', [OfflineCostController::class, 'downloadCategoryPdf'])->name('category.download.pdf');
    });
    // Offline Cost Creation
    Route::get('/offline-cost/create', [OfflineCostController::class, 'create']
    )->name('offline.cost.create');
    // Offline Cost store
    Route::post('/offline-cost/store', [OfflineCostController::class, 'store']
    )->name('offline.cost.store');
    // Offline Cost Edit
    Route::get('/offline-cost/edit/{id}', [OfflineCostController::class, 'edit']
    )->name('offline.cost.transaction.edit');
    //Offline Cost Update
    Route::put('/offline-cost/update-transaction/{id}', [OfflineCostController::class, 'update']
    )->name('offline.cost.transaction.update');
    // Offline Cost Delete
    Route::delete('/offline-cost/delete/{id}', [OfflineCostController::class, 'destroy']
    )->name('offline.cost.transaction.destroy');
    // Offline Cost Payment Update
    Route::post('/offline-cost/update-payment/{id}', [OfflineCostController::class, 'updatePayment'])
    ->name('offline.cost.payment.update');
    // Offline Cost Approve Status Update
    Route::post('/offline-cost/update-approval/{id}', [OfflineCostController::class, 'updateApprovalStatus'])
    ->name('offline.approve.status.update');
    // Offline Cost Report
    Route::get('/offline-cost/report', [OfflineCostController::class, 'report']
    )->name('offline.cost.report');
    // Offline Cost PDF Download
    Route::get('/offline-cost/report/pdf', [OfflineCostController::class, 'downloadReportPdf']
    )->name('offline.cost.download.pdf');
    

    
    
    // Office Online Cost Management

    // Online Cost Category
    Route::post('/online-cost/category', [OnlineCostController::class, 'onlineCostCategory']
    )->name('online.category.create');
    // Online Cost Creation
    Route::get('/online-cost/create', [OnlineCostController::class, 'create']
    )->name('online.cost.create');
    // Online Cost store
    Route::post('/online-cost/store', [OnlineCostController::class, 'store']
    )->name('online.cost.store');
    // Online Cost Report
    Route::get('/online-cost/report', [OnlineCostController::class, 'report']
    )->name('online.cost.report');
    // Online Cost Update
    Route::put('/online-cost/update/{id}', [OnlineCostController::class, 'update'])
    ->name('online.cost.update');



    // Loan and Installment Management

    // Loan Creation
    Route::get('/loan/create', [LoanAndInstallmentController::class, 'createLoan']
    )->name('loan.create');
    // Loan store
    Route::post('/loan/store', [LoanAndInstallmentController::class, 'storeLoan']
    )->name('loan.store');
    // Installment Creation
    Route::get('/installment/create', [LoanAndInstallmentController::class, 'createInstallment']
    )->name('installment.create');
    // Installment store
    Route::post('/installment/store', [LoanAndInstallmentController::class, 'storeInstallment']
    )->name('installment.store');
    // Loan Report
    Route::get('/loan/report', [LoanAndInstallmentController::class, 'report']
    )->name('loan.report');
    // Loan Installments
    Route::get('/loan/{loan}/installments', [LoanAndInstallmentController::class, 'showInstallments']
    )->name('loan.installments');
    // Update Installment Status
    Route::post('/installments/{installment}/toggle-status', [LoanAndInstallmentController::class, 'toggleInstallmentStatus'])
    ->name('installment.toggle-status');



    // Product Management

    // Product Summary
    Route::get('/product/summary', [ProductController::class, 'index'])
    ->name('product.summary');

    //Product Category
        Route::get('/product/category', [ProductController::class, 'category'])
        ->name('product.category');
        // Product Category Store
        Route::post('/product/category/store', [ProductController::class, 'storeCategory'])
        ->name('product.category.store');
        // Product Category Edit
        Route::get('/categories/{id}/edit', [ProductController::class, 'editCategory'])
        ->name('product.category.edit');
        // Product Category Update
        Route::put('/categories/{id}', [ProductController::class, 'updateCategory'])
        ->name('product.category.update');
        // Product Category Delete
        Route::delete('/categories/{id}', [ProductController::class, 'destroyCategory'])
        ->name('product.category.destroy');
        // Product Category Download PDF
        Route::get('/product/category/download/pdf', [ProductController::class, 'downloadCategoryPdf'])
        ->name('product.category.download.pdf');
        
    //Product
        Route::get('/product/add', [ProductController::class, 'create'])
        ->name('product.add');
        // Store Product
        Route::post('/products/store', [ProductController::class, 'store'])
        ->name('product.store');
        // Product Edit
        Route::get('/products/{id}/edit', [ProductController::class, 'editProduct'])
        ->name('product.edit');
        // Update Product
        Route::put('/products/{id}', [ProductController::class, 'updateProduct'])
        ->name('product.update');
        // Product Delete
        Route::delete('/products/{id}', [ProductController::class, 'destroyProduct'])
        ->name('product.destroy');
        // Approve Product
        Route::post('/products/{id}/approve', [ProductController::class, 'approveProduct'])
        ->name('product.approve');
        // Product download PDF
        Route::get('/products/download/pdf', [ProductController::class, 'downloadProductPdf'])
        ->name('product.download.pdf');

    // Sell Products
        Route::get('/products/sell', [ProductController::class, 'sell'])
        ->name('product.sell');
        Route::post('/products/sell/store', [ProductController::class, 'storeSoldProduct'])
        ->name('product.sell.store');
        Route::get('/products/sell/{id}/edit', [ProductController::class, 'editSale'])
        ->name('product.sell.edit');
        Route::put('/products/sell/{id}', [ProductController::class, 'updateSale'])
        ->name('product.sell.update');
        Route::delete('/products/sell/{id}', [ProductController::class, 'destroySale'])
        ->name('product.sell.destroy');
        Route::post('/products/sell/{id}/approve', [ProductController::class, 'approveSale'])
        ->name('product.sell.approve');
        Route::get('/products/sales/download/pdf', [ProductController::class, 'downloadProductSalesPdf'])
        ->name('product.sales.download.pdf');


    // Loss Products
        Route::get('/products/loss', [ProductController::class, 'loss'])
        ->name('product.loss');
        Route::post('/products/loss/store', [ProductController::class, 'storeLossProduct'])
        ->name('product.loss.store');
        Route::get('/products/loss/{id}/edit', [ProductController::class, 'editLoss'])
        ->name('product.loss.edit');
        Route::put('/products/loss/{id}', [ProductController::class, 'updateLoss'])
        ->name('product.loss.update');
        Route::delete('/products/loss/{id}', [ProductController::class, 'destroyLoss'])
        ->name('product.loss.destroy');
        Route::post('/products/loss/{id}/approve', [ProductController::class, 'approveLoss'])
        ->name('product.loss.approve');
        Route::get('/products/loss/download/pdf', [ProductController::class, 'downloadLossPdf'])
        ->name('product.loss.download.pdf');


    // Return Products
        Route::get('/products/return', [ProductController::class, 'return'])
        ->name('product.return');
        Route::post('/products/return/store', [ProductController::class, 'storeReturnProduct'])
        ->name('product.return.store');
        Route::get('/products/return/{id}/edit', [ProductController::class, 'editReturn'])
        ->name('product.return.edit');
        Route::put('/products/return/{id}', [ProductController::class, 'updateReturn'])
        ->name('product.return.update');
        Route::delete('/products/return/{id}', [ProductController::class, 'destroyReturn'])
        ->name('product.return.destroy');
        Route::post('/products/return/{id}/approve', [ProductController::class, 'approveReturn'])
        ->name('product.return.approve');
        Route::get('/products/return/download/pdf', [ProductController::class, 'downloadReturnPdf'])
        ->name('product.return.download.pdf');


    // Product Report
        Route::get('/product/report', [ProductController::class, 'allCategoryReport'])
        ->name('product.category.report');
        Route::get('/product/report/pdf', [ProductController::class, 'downloadAllCategoryPdf'])
        ->name('product.category.report.pdf');
        Route::get('/product/single-category/report', [ProductController::class, 'singleCategoryReport'])
        ->name('product.single.category.report');
        // Product Stock Report
        Route::get('/product/stock/report', [ProductController::class, 'stockReport'])
        ->name('product.stock.report');
        Route::post('/product/stock/status-update', [ProductController::class, 'updateStockStatus'])
        ->name('product.stock.status.update');
        // Product Stock filter Pdf
        Route::get('/products/stock/pdf', [ProductController::class, 'downloadProductFilterPdf'])
        ->name('product.stock.filter.pdf');
        // Product Sell Report
        Route::get('/product/sell/report', [ProductController::class, 'sellReport'])
        ->name('product.sell.report');
        Route::post('/product/sell/status-update', [ProductController::class, 'updateSellStatus'])
        ->name('product.sell.status.update');
        // Product sell filter Pdf
        Route::get('/products/sell/pdf', [ProductController::class, 'downloadSellFilterPdf'])
        ->name('product.sell.filter.pdf');
        // Product Loss Report
        Route::get('/product/loss/report', [ProductController::class, 'lossReport'])
        ->name('product.loss.report');
        Route::post('/product/loss/status-update', [ProductController::class, 'updateLossStatus'])
        ->name('product.loss.status.update');
        // Product loss filter Pdf
        Route::get('/products/loss/pdf', [ProductController::class, 'downloadLossFilterPdf'])
        ->name('product.loss.filter.pdf');
        // Product Return Report
        Route::get('/product/return/report', [ProductController::class, 'returnReport'])
        ->name('product.return.report');
        Route::post('/product/return/status-update', [ProductController::class, 'updateReturnStatus'])
        ->name('product.return.status.update');
        // Product return filter Pdf
        Route::get('/products/return/pdf', [ProductController::class, 'downloadReturnFilterPdf'])
        ->name('product.return.filter.pdf');

    
    
    // Company Own Project Management

    // Create Company Own Project
    Route::get('/company/project/create', [CompanyOwnProjectController::class, 'create'])
    ->name('company.project.create');
    // Store Company Own Project
    Route::post('/company/project/store', [CompanyOwnProjectController::class, 'store'])
    ->name('company.project.store');
    // Create Project Transaction
    Route::get('/company/project/transaction/add', [CompanyOwnProjectController::class, 'transaction'])
    ->name('company.project.transaction.add');
    // Store Project Transaction
    Route::post('/company/project/transaction/store', [CompanyOwnProjectController::class, 'storeTransaction'])
    ->name('company.project.transaction.store');
    // Company Project List
    Route::get('/company/project/list', [CompanyOwnProjectController::class, 'list'])
    ->name('company.project.list');
    // Show Company Project Transactions
    Route::get('/company-projects/{id}/transactions', [CompanyOwnProjectController::class, 'getTransactions'])
    ->name('company.project.transactions');
    // Delete Project
    Route::post('/company/project/delete/{id}', [CompanyOwnProjectController::class, 'deleteProject'])
    ->name('company.project.delete');



    // Client Project Management

    // Create Client Project
    Route::get('/client/project/create', [ClientProjectController::class, 'create'])
    ->name('client.project.create');
    // Store Client Project
    Route::post('/client/project/store', [ClientProjectController::class, 'store'])
    ->name('client.project.store');
    // Create Project Transaction
    Route::get('/client/project/transaction/add', [ClientProjectController::class, 'transaction'])
    ->name('client.project.transaction.add');
    // Store Project Transaction
    Route::post('/client/project/transaction/store', [ClientProjectController::class, 'storeTransaction'])
    ->name('client.project.transaction.store');
    // Create Client Debit 
    Route::get('/client/debit/add', [ClientProjectController::class, 'createClientDebit'])
    ->name('client.debit.add');
    // Store Client Debit
    Route::post('/client/debit/store', [ClientProjectController::class, 'storeClientDebit'])
    ->name('client.debit.store');
    // Client Project List
    Route::get('/client/project/list', [ClientProjectController::class, 'list'])
    ->name('client.project.list');
    // Show Client Project Transactions
    Route::get('/client-projects/{id}/transactions', [ClientProjectController::class, 'getTransactions'])
    ->name('client.project.transactions');
    // Show Client Debits
    Route::get('/client-projects/{id}/debits', [ClientProjectController::class, 'getClientDebits'])
    ->name('client.debits');
    // Update Client Debit Status
    Route::post('/client-debits/{id}/toggle-status', [ClientProjectController::class, 'toggleDebitStatus'])
    ->name('client.debit.toggle-status');
    // Delete Project
    Route::post('/client/project/delete/{id}', [ClientProjectController::class, 'deleteProject'])
    ->name('client.project.delete');



    // Revenue and Target Management

    // Summary
    Route::get('/target/summary', [RevenueAndTargetController::class, 'index']
    )->name('target.summary');
    // Revenue Report
    Route::get('/revenue/report', [RevenueAndTargetController::class, 'revenueReport']
    )->name('revenue.report');
    // Expense Report
    Route::get('/expense/report', [RevenueAndTargetController::class, 'expenseReport']
    )->name('expense.report');


    
    // Priority Management

    // Add Priority
    Route::get('/priority/add', [PriorityController::class, 'create'])
    ->name('priority.add');
    // Store Priority
    Route::post('/priority/store', [PriorityController::class, 'store'])
    ->name('priority.store');
    // List Priorities
    Route::get('/priority/list', [PriorityController::class, 'index'])
    ->name('priority.list');
    // Edit Priority
    Route::get('/priority/edit/{priority}', [PriorityController::class, 'edit'])
    ->name('priority.edit');
    // Update Priority
    Route::put('/priority/update/{priority}', [PriorityController::class, 'update'])
    ->name('priority.update');
    // Delete Priority
    Route::delete('/priority/delete/{priority}', [PriorityController::class, 'destroy'])
    ->name('priority.delete');
    // Purchased Priority
    Route::put('/priority/{priority}/purchase', [PriorityController::class, 'purchase'])
    ->name('priority.purchase');


    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});



// Protected Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users/role',[RoleController::class, 'store'])->name('admin.addrole');
    Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});


require __DIR__.'/auth.php';
