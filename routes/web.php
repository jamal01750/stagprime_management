<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreditOrDebitController;
use App\Http\Controllers\AdminUserController;
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
    // Show Reports for Credit or Debit 
    Route::post('/credit-debit/report', [CreditOrDebitController::class, 'showReports']
    )->name('credit.debit.report.show');
    // Download PDF for Credit or Debit
    Route::post('/credit-debit/report/pdf', [CreditOrDebitController::class, 'downloadPDF']
    )->name('credit.debit.download.pdf');



    // Student/Internship Routes

        // Batch Creation
        Route::post('/student-internship/batch', [InternshipController::class, 'createBatch']
        )->name('batch.create');
        // Course Creation
        Route::post('/student-internship/course', [InternshipController::class, 'createCourse']
        )->name('course.create');

        // Student/Internship summary
        Route::get('/student-internship/summary', [StudentController::class, 'summary']
        )->name('student.internship.summary');


        // Student Registration
        Route::get('/student-registration/new', [StudentController::class, 'newRegistration']
        )->name('student.registration');
        // Student Registration Data Store
        Route::post('/student-registration/store', [StudentController::class, 'store']
        )->name('student.registration.store');
        // Student List Running
        Route::get('/student-list/running', [StudentController::class, 'runningList']
        )->name('student.list.running');
        // Student List Expire
        Route::get('/student-list/expire', [StudentController::class, 'expireList']
        )->name('student.list.expire');
        // Student Payment Update
        Route::get('/student/payment/update/{id}', [StudentController::class, 'paymentUpdate']
        )->name('student.payment.update');
        // Student Active Status Update
        Route::post('/student/active-status/update/{id}', [StudentController::class, 'updateActiveStatus']
        )->name('student.active.status.update');
        // Student Individual Details
        Route::get('/student/individual/{id}', [StudentController::class, 'studentDetails']
        )->name('student.individual');
        // Student Individual Edit
        Route::get('/student/edit/{id}', [StudentController::class, 'editStudent']
        )->name('student.edit');
        // Student Individual Update
        Route::put('/student/update/{id}', [StudentController::class, 'updateStudent']
        )->name('student.update');


        // Internship Registration 
        Route::get('/internship-registration/new', [InternshipController::class, 'newRegistration']
        )->name('internship.registration');
        // Internship Registration data Store
        Route::post('/internship-registration/store', [InternshipController::class, 'store']
        )->name('internship.registration.store');
        // Internship List Running
        Route::get('/internship-list/running', [InternshipController::class, 'runningList']
        )->name('internship.list.running');
        // Internship List Expire
        Route::get('/internship-list/expire', [InternshipController::class, 'expireList']
        )->name('internship.list.expire');
        // Internship Payment Update
        Route::get('/internship/payment/update/{id}', [InternshipController::class, 'paymentUpdate']
        )->name('internship.payment.update');
        // Internship Active Status Update
        Route::post('/internship/active-status/update/{id}', [InternshipController::class, 'updateActiveStatus']
        )->name('internship.active.status.update');
        // Internship Individual Details
        Route::get('/internship/individual/{id}', [InternshipController::class, 'studentDetails']
        )->name('internship.individual');
        // Internship Individual Edit
        Route::get('/internship/edit/{id}', [InternshipController::class, 'editStudent']
        )->name('internship.edit');
        // Internship Individual Update
        Route::put('/internship/update/{id}', [InternshipController::class, 'updateStudent']
        )->name('internship.update');



    // Staff salary Management    
    
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
    // Staff Salary Report
    Route::get('/staff/salary/report', [StaffSalaryController::class, 'staffSalaryReport']
    )->name('staff.report');
    // Staff Salary Update
    Route::put('/staff/salary/update/{id}', [StaffSalaryController::class, 'updateStaffSalary']
    )->name('staff.salary.update');
    

    // Office Offline Cost Management

    // Offline Cost Category
    Route::post('/offline-cost/category', [OfflineCostController::class, 'offlineCostCategory']
    )->name('offline.category.create');
    // Offline Cost Creation
    Route::get('/offline-cost/create', [OfflineCostController::class, 'create']
    )->name('offline.cost.create');
    // Offline Cost store
    Route::post('/offline-cost/store', [OfflineCostController::class, 'store']
    )->name('offline.cost.store');
    // Offline Cost Report
    Route::get('/offline-cost/report', [OfflineCostController::class, 'report']
    )->name('offline.cost.report');
    // Offline Cost Update
    Route::put('/offline-cost/update/{id}', [OfflineCostController::class, 'update'])
    ->name('offline.cost.update');

    
    
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
    Route::get('/revenueandtarget', [RevenueAndTargetController::class, 'index']
    )->name('revenueandtarget');
    Route::post('/settarget', [RevenueAndTargetController::class, 'settarget']
    )->name('settarget');
    Route::get('/yearlysummary', [RevenueAndTargetController::class, 'index']
    )->name('yearlysummary');


    
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
