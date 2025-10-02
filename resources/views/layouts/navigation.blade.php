
<aside 
    x-data="sidebarNav()" 
    x-init="initMenu()" 
    class="w-48 min-h-screen bg-white border-r border-gray-200 p-2 shadow-lg flex flex-col"
>
    <!-- Logo -->
    <div class="items-center p-2">
        <a href="{{ route('home') }}" class="flex items-center">
            <img src="{{ asset('images/logo.png') }}" alt="Stagprime Logo">
        </a>
    </div>

    <nav class="flex-1">
        <ul class="space-y-1">

            <!-- Dashboard -->
            <li>
                <a href="{{ route('dashboard') }}"
                   :class="navActive('dashboard')"
                   class="flex items-center px-3 py-2 rounded transition hover:bg-blue-100 font-medium">
                    Dashboard
                </a>
            </li>

            <!-- Daily Credit & Debit -->
            <li>
                <button @click="toggleMenu('credit')" class="flex justify-between text-left items-center w-full px-3 py-2 rounded text-gray-700 font-medium hover:bg-blue-100 focus:outline-none">
                    <span>Credit & Debit</span>
                    <svg :class="{'rotate-90': openMenu.credit}" 
                        class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    
                    </svg>
                </button>
                <ul x-show="openMenu.credit" x-transition
                    class="mt-1 ml-4 space-y-1 border-l border-gray-300 pl-3">
                    <li>
                        <a href="{{ route('credit.debit.summary') }}" :class="navActive('credit.debit.summary')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Summary
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('credit.debit.transaction') }}" :class="navActive('credit.debit.transaction')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Add Credit/Debit
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('credit.debit.report') }}" :class="navActive('credit.debit.report')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Report
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Office Student / Internship (Nested Submenu Example) -->
            <li>
                <button @click="toggleMenu('office')" class="flex justify-between text-left items-center w-full px-3 py-2 rounded text-gray-700 font-medium hover:bg-blue-100 focus:outline-none">
                    <span>Student / Internship</span>
                    <svg :class="{'rotate-90': openMenu.office}" 
                        class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    
                    </svg>
                </button>
                <ul x-show="openMenu.office" x-transition class="ml-6 mt-1 space-y-1">
                    <!-- <li>
                        <a href="{{ route('student.internship.summary') }}" :class="navActive('student.internship.summary')" class="block px-3 py-2 rounded hover:bg-green-100">Summary</a>
                    </li> -->
                    <!-- Registration Nested Submenu -->
                    <li>
                        <button @click="toggleMenu('registration')" class="flex items-center w-full px-3 py-2 rounded transition hover:bg-blue-100 font-medium focus:outline-none">
                            <span>Registration</span>
                            <svg :class="{'rotate-90': openMenu.registration}" 
                                class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    
                            </svg>
                        </button>
                        <ul x-show="openMenu.registration" x-transition class="ml-6 mt-1 space-y-1">
                            <li>
                                <a href="{{ route('student.registration') }}" :class="navActive('student.registration')" class="block px-3 py-2 rounded hover:bg-green-100">New Student</a>
                            </li>
                            <li>
                                <a href="{{ route('internship.registration') }}" :class="navActive('internship.registration')" class="block px-3 py-2 rounded hover:bg-green-100">New Intern</a>
                            </li>
                        </ul>
                    </li>
                    <!-- Student Payment form -->
                    <li>
                        <a href="{{ route('student.payment') }}" :class="navActive('student.payment')" class="block px-3 py-2 rounded hover:bg-green-100">Student Payment</a>
                    </li>
                    <!-- Student Lists Nested Submenu -->
                    <li>
                        <button @click="toggleMenu('studentLists')" class="flex items-center w-full px-3 py-2 rounded transition hover:bg-blue-100 font-medium focus:outline-none">
                            <span>Student Lists</span>
                            <svg :class="{'rotate-90': openMenu.studentLists}" 
                                class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    
                            </svg>
                        </button>
                        <ul x-show="openMenu.studentLists" x-transition class="ml-6 mt-1 space-y-1">
                            <li>
                                <a href="{{ route('student.list.running') }}" :class="navActive('student.list.running')" class="block px-3 py-2 rounded hover:bg-green-100">Running Students</a>
                            </li>
                            <li>
                                <a href="{{ route('student.list.expire') }}" :class="navActive('student.list.expire')" class="block px-3 py-2 rounded hover:bg-green-100">Ex-Students</a>
                            </li>
                        </ul>
                    </li>
                    <!-- Intern Lists Nested Submenu -->
                    <li>
                        <button @click="toggleMenu('internLists')" class="flex items-center w-full px-3 py-2 rounded transition hover:bg-blue-100 font-medium focus:outline-none">
                            <span>Intern Lists</span>
                            <svg :class="{'rotate-90': openMenu.internLists}" 
                                class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    
                            </svg>
                        </button>
                        <ul x-show="openMenu.internLists" x-transition class="ml-6 mt-1 space-y-1">
                            <li>
                                <a href="{{ route('internship.list.running') }}" :class="navActive('internship.list.running')" class="block px-3 py-2 rounded hover:bg-green-100">Running Interns</a>
                            </li>
                            <li>
                                <a href="{{ route('internship.list.expire') }}" :class="navActive('internship.list.expire')" class="block px-3 py-2 rounded hover:bg-green-100">Ex-Interns</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>

            <!-- Staff Salary -->
            <li>
                <button 
                    @click="toggleMenu('staff')"
                    class="flex justify-between text-left items-center w-full px-3 py-2 rounded text-gray-700 font-medium hover:bg-blue-100 focus:outline-none"
                >
                    <span>Staff Salary</span>
                    <svg :class="{'rotate-90': openMenu.staff}" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="openMenu.staff" x-transition class="mt-1 ml-4 space-y-1 border-l border-gray-300 pl-3">
                    <li>
                        <a href="{{ route('staff.summary') }}" :class="navActive('staff.summary')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Summary 
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('staff.create') }}" :class="navActive('staff.create')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Add staff
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('staff.salary.list') }}" :class="navActive('staff.salary.list')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Salary List
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('staff.report') }}" :class="navActive('staff.report')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Report
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Office Offline Cost -->
            <li>
                <button 
                    @click="toggleMenu('offline')"
                    class="flex justify-between text-left items-center w-full px-3 py-2 rounded text-gray-700 font-medium hover:bg-blue-100 focus:outline-none"
                >
                    <span>Offline Cost</span>
                    <svg :class="{'rotate-90': openMenu.offline}" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="openMenu.offline" x-transition class="mt-1 ml-4 space-y-1 border-l border-gray-300 pl-3">
                    <li>
                        <a href="{{ route('offline.cost.create') }}" :class="navActive('offline.cost.create')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Add Offline Cost
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('offline.cost.report') }}" :class="navActive('offline.cost.report')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Monthly Report
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Office Online Cost -->
            <li>
                
                <button 
                    @click="toggleMenu('online')"
                    class="flex justify-between text-left items-center w-full px-3 py-2 rounded text-gray-700 font-medium hover:bg-blue-100 focus:outline-none"
                >
                    <span>Online Cost</span>
                    <svg :class="{'rotate-90': openMenu.online}" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="openMenu.online" x-transition class="mt-1 ml-4 space-y-1 border-l border-gray-300 pl-3">
                    <li>
                        <a href="{{ route('online.cost.create') }}" :class="navActive('online.cost.create')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Add Online Cost
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('online.cost.report') }}" :class="navActive('online.cost.report')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Monthly Report
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Loan and Installment -->
            <li>
                
                <button 
                    @click="toggleMenu('loan')"
                    class="flex justify-between text-left items-center w-full px-3 py-2 rounded text-gray-700 font-medium hover:bg-blue-100 focus:outline-none"
                >
                    <span>Loan and Installment</span>
                    <svg :class="{'rotate-90': openMenu.loan}" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="openMenu.loan" x-transition class="mt-1 ml-4 space-y-1 border-l border-gray-300 pl-3">
                    
                    <li>
                        <a href="{{ route('loan.create') }}" :class="navActive('loan.create')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Add Loan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('installment.create') }}" :class="navActive('installment.create')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Add Installment
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('loan.report') }}" :class="navActive('loan.report')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Report
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Product Management -->
            <li>
                
                <button 
                    @click="toggleMenu('product')"
                    class="flex justify-between text-left items-center w-full px-3 py-2 rounded text-gray-700 font-medium hover:bg-blue-100 focus:outline-none"
                >
                    <span>Product Management</span>
                    <svg :class="{'rotate-90': openMenu.product}" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="openMenu.product" x-transition class="mt-1 ml-4 space-y-1 border-l border-gray-300 pl-3">
                    
                    <li>
                        <a href="{{ route('product.summary') }}" :class="navActive('product.summary')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Summary
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('product.category') }}" :class="navActive('product.category')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Product Category
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('product.add') }}" :class="navActive('product.add')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Add Product
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('product.sell') }}" :class="navActive('product.sell')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Sell Product
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('product.loss') }}" :class="navActive('product.loss')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Loss Product
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('product.return') }}" :class="navActive('product.return')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Return Product
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('product.report') }}" :class="navActive('product.report')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Report 
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Company Own Projects -->
            <li>
                
                <button 
                    @click="toggleMenu('company')"
                    class="flex justify-between text-left items-center w-full px-3 py-2 rounded text-gray-700 font-medium hover:bg-blue-100 focus:outline-none"
                >
                    <span>Own Projects</span>
                    <svg :class="{'rotate-90': openMenu.company}" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="openMenu.company" x-transition class="mt-1 ml-4 space-y-1 border-l border-gray-300 pl-3">
                    
                    <li>
                        <a href="{{ route('company.project.create') }}" :class="navActive('company.project.create')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Add Project
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('company.project.transaction.add') }}" :class="navActive('company.project.transaction.add')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Add Transaction
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('company.project.list') }}" :class="navActive('company.project.list')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Project Details
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Client Projects -->
            <li>
                
                <button 
                    @click="toggleMenu('client')"
                    class="flex justify-between text-left items-center w-full px-3 py-2 rounded text-gray-700 font-medium hover:bg-blue-100 focus:outline-none"
                >
                    <span>Client Projects</span>
                    <svg :class="{'rotate-90': openMenu.client}" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="openMenu.client" x-transition class="mt-1 ml-4 space-y-1 border-l border-gray-300 pl-3">
                    
                    <li>
                        <a href="{{ route('client.project.create') }}" :class="navActive('client.project.create')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Add Project
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('client.project.transaction.add') }}" :class="navActive('client.project.transaction.add')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Add Transaction
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('client.debit.add') }}" :class="navActive('client.debit.add')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Client Debit
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('client.project.list') }}" :class="navActive('client.project.list')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Project Details
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Monthly Revenue & Target -->
            <li>
                
                <button 
                    @click="toggleMenu('target')"
                    class="flex justify-between text-left items-center w-full px-3 py-2 rounded text-gray-700 font-medium hover:bg-blue-100 focus:outline-none"
                >
                    <span>Monthly Revenue & Target</span>
                    <svg :class="{'rotate-90': openMenu.target}" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="openMenu.target" x-transition class="mt-1 ml-4 space-y-1 border-l border-gray-300 pl-3">
                    
                    <li>
                        <a href="{{ route('target.summary') }}" :class="navActive('target.summary')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Summary
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('revenue.report') }}" :class="navActive('revenue.report')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Revenue Report
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('expense.report') }}" :class="navActive('expense.report')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Expense Report
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Office Priority Products/Projects -->
            <li>
                
                <button 
                    @click="toggleMenu('priority')"
                    class="flex justify-between text-left items-center w-full px-3 py-2 rounded text-gray-700 font-medium hover:bg-blue-100 focus:outline-none"
                >
                    <span>Priority Products/Projects</span>
                    <svg :class="{'rotate-90': openMenu.priority}" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="openMenu.priority" x-transition class="mt-1 ml-4 space-y-1 border-l border-gray-300 pl-3">
                    
                    <li>
                        <a href="{{ route('priority.add') }}" :class="navActive('priority.add')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                Add New
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('priority.list') }}" :class="navActive('priority.list')"
                            class="block px-3 py-2 rounded hover:bg-green-100">
                                List
                        </a>
                    </li>
                </ul>
            </li>
            

            <!-- User Settings (Admin Only) -->
            @auth
                @if(auth()->user()->role === 'admin')
                    <li>
                        <a class="block px-3 py-2 rounded hover:bg-blue-100 text-gray-700 font-medium"
                        href="{{ route('admin.users.index') }}">
                            User Settings
                        </a>
                    </li>
                @endif
            @endauth

            <li><a class="block px-3 py-2 rounded hover:bg-blue-100 text-gray-700 font-medium" href="#online">Reports & Analytics</a></li>

        </ul>
    </nav>
</aside>

<script>
    function sidebarNav() {
        return {
            openMenu: {
                credit: false,
                office: false,
                registration: false,
                studentLists: false,
                internLists: false,
                staff:false,
                offline: false,
                online: false,
                loan: false,
                product: false,
                company: false,
                client: false,
                target: false,
                priority: false,
                
                // Add other submenu keys here
            },
            routeName: '{{ request()->route()->getName() }}',
            // Map route names to parent/submenu keys for auto-opening
            menuMap: {
                'credit.debit.summary': ['credit'],
                'credit.debit.transaction': ['credit'],
                'credit.debit.report': ['credit'],
                'student.internship.summary': ['office'],
                'student.registration': ['office', 'registration'],
                'internship.registration': ['office', 'registration'],
                'student.list.running': ['office', 'studentLists'],
                'student.list.expire': ['office', 'studentLists'],
                'internship.list.running': ['office', 'internLists'],
                'internship.list.expire': ['office', 'internLists'],
                'internship.registration.lists': ['office'],
                'staff.summary': ['staff'],
                'staff.create': ['staff'],
                'staff.salary.list': ['staff'],
                'staff.report': ['staff'],
                'offline.cost.create': ['offline'],
                'offline.cost.report': ['offline'],
                'online.cost.create': ['online'],
                'online.cost.report': ['online'],
                'loan.create': ['loan'],
                'installment.create': ['loan'],
                'loan.report': ['loan'],
                'product.summary': ['product'],
                'product.category': ['product'],
                'product.add': ['product'],
                'product.sell': ['product'],
                'product.loss': ['product'],
                'product.return': ['product'],
                'product.report': ['product'],
                'company.project.create': ['company'],
                'company.project.transaction.add': ['company'],
                'company.project.list': ['company'],
                'client.project.create': ['client'],
                'client.project.transaction.add': ['client'],
                'client.debit.add': ['client'],
                'client.project.list': ['client'],
                'target.summary': ['target'],
                'revenue.report': ['target'],
                'expense.report': ['target'],
                'priority.add': ['priority'],
                'priority.list': ['priority'],
                // Add other route names and their parent keys here
            },
            initMenu() {
                let parents = this.menuMap[this.routeName];
                if (parents) {
                    parents.forEach(key => this.openMenu[key] = true);
                }
            },
            toggleMenu(menu) {
                this.openMenu[menu] = !this.openMenu[menu];
            },
            navActive(route) {
                return this.routeName === route
                    ? 'bg-green-200 text-green-800 font-semibold'
                    : 'text-gray-700';
            }
        }
    }
</script>

