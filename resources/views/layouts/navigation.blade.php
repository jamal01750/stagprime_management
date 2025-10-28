<aside 
    class="fixed inset-y-0 left-0 z-50 flex-shrink-0 w-64 overflow-y-auto bg-white border-r border-slate-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0"
    :class="{'-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen}"
    x-data="sidebarNav()" 
    x-init="initMenu()"
    x-cloak>

    <div class="flex items-center justify-center p-4 border-b border-slate-200 h-[65px]">
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 w-auto">
        </a>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1">
        <a href="{{ route('dashboard') }}" :class="navLinkClass('dashboard')" class="flex items-center px-3 py-2.5 rounded-lg font-medium">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
            Dashboard
        </a>
        
        @php
            $navItems = [
                'credit' => ['label' => 'Credit & Debit', 'icon' => '<svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15A2.25 2.25 0 002.25 6.75v10.5A2.25 2.25 0 004.5 19.5z" /></svg>', 'children' => [
                    ['route' => 'credit.debit.summary', 'label' => 'Summary'],
                    ['route' => 'credit.debit.transaction', 'label' => 'Add Credit/Debit'],
                    ['route' => 'credit.debit.report', 'label' => 'Report'],
                ]],
                'office' => ['label' => 'Student / Internship', 'icon' => '<svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>', 'children' => [
                    'registration' => ['label' => 'Registration', 'children' => [
                        ['route' => 'batch.course', 'label' => 'Batch & Course'],
                        ['route' => 'student.registration', 'label' => 'New Student'],
                        ['route' => 'internship.registration', 'label' => 'New Intern'],
                    ]],
                    ['route' => 'student.payment', 'label' => 'Students Payment'],
                    ['route' => 'students.list', 'label' => 'Students List'],
                    ['route' => 'internship.list', 'label' => 'Interns List'],
                    
                ]],
                'staff' => ['label' => 'Staff Salary', 'icon' => '<svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-4.663M12 3.375c-3.418 0-6.162 2.759-6.162 6.188s2.744 6.188 6.162 6.188 6.162-2.759 6.162-6.188-2.744-6.188-6.162-6.188z" /></svg>', 'children' => [
                    ['route' => 'staff.summary', 'label' => 'Summary'],
                    ['route' => 'staff.create', 'label' => 'Add Staff'],
                    ['route' => 'staff.salary.list', 'label' => 'Salary List'],
                    ['route' => 'staff.report', 'label' => 'Report'],
                ]],
                 'offline' => ['label' => 'Offline Cost', 'icon' => '<svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414-.336.75-.75.75h-.75m0-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75-.75v-.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /></svg>', 'children' => [
                    ['route' => 'offline.category', 'label' => 'Offline Category'],
                    ['route' => 'offline.cost.create', 'label' => 'Add Offline Cost'],
                    ['route' => 'offline.cost.report', 'label' => 'Monthly Report'],
                ]],
                 'online' => ['label' => 'Online Cost', 'icon' => '<svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" /></svg>', 'children' => [
                    ['route' => 'online.cost.create', 'label' => 'Add Online Cost'],
                    ['route' => 'online.cost.report', 'label' => 'Monthly Report'],
                ]],
                 'loan' => ['label' => 'Loan and Installment', 'icon' => '<svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>', 'children' => [
                    ['route' => 'loan.create', 'label' => 'Add Loan'],
                    ['route' => 'installment.create', 'label' => 'Add Installment'],
                    ['route' => 'loan.report', 'label' => 'Report'],
                ]],
                'product' => ['label' => 'Product Management', 'icon' => '<svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>', 'children' => [
                    ['route' => 'product.summary', 'label' => 'Summary'],
                    ['route' => 'product.category', 'label' => 'Product Category'],
                    ['route' => 'product.add', 'label' => 'Add Product'],
                    ['route' => 'product.sell', 'label' => 'Sell Product'],
                    ['route' => 'product.loss', 'label' => 'Loss Product'],
                    ['route' => 'product.return', 'label' => 'Return Product'],
                    'report' => ['label' => 'Report', 'children' => [
                        ['route' => 'product.report', 'label' => 'All Report'],
                        ['route' => 'product.stock.report', 'label' => 'Stock Report'],
                        ['route' => 'product.sell.report', 'label' => 'Sell Report'],
                        ['route' => 'product.loss.report', 'label' => 'Loss Report'],
                        ['route' => 'product.return.report', 'label' => 'Return Report'],
                    ]],
                ]],
                 'company' => ['label' => 'Own Projects', 'icon' => '<svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5v12m-12 0h3.75" /></svg>', 'children' => [
                    ['route' => 'company.project.create', 'label' => 'Add Project'],
                    ['route' => 'company.project.transaction.add', 'label' => 'Add Transaction'],
                    ['route' => 'company.project.list', 'label' => 'Project Details'],
                ]],
                 'client' => ['label' => 'Client Projects', 'icon' => '<svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM10.5 16.5h-1.5" /></svg>', 'children' => [
                    ['route' => 'client.project.create', 'label' => 'Add Project'],
                    ['route' => 'client.project.transaction.add', 'label' => 'Add Transaction'],
                    ['route' => 'client.debit.add', 'label' => 'Client Debit'],
                    ['route' => 'client.project.list', 'label' => 'Project Details'],
                ]],
                'target' => ['label' => 'Revenue & Target', 'icon' => '<svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414-.336.75-.75.75h-.75m0-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75-.75v-.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75" /></svg>', 'children' => [
                    ['route' => 'target.summary', 'label' => 'Summary'],
                    ['route' => 'revenue.report', 'label' => 'Revenue Report'],
                    ['route' => 'expense.report', 'label' => 'Expense Report'],
                ]],
                'priority' => ['label' => 'Priority Items', 'icon' => '<svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.32 1.011l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.32-1.011l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>', 'children' => [
                    ['route' => 'priority.add', 'label' => 'Add New'],
                    ['route' => 'priority.list', 'label' => 'List'],
                ]],
                'reports' => ['label' => 'Reports & Analytics', 'icon' => '<svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>', 'children' => [
                    ['route' => '#', 'label' => 'Financial Summary'],
                    ['route' => '#', 'label' => 'Project/Client'],
                    ['route' => '#', 'label' => 'Target vs Achievement'],
                ]],
            ];

            function renderNav($items, $level = 1) {
                $ulClass = 'space-y-1';
                $marginLeft = 'ml-5';
                if ($level > 1) {
                    $ulClass .= ' pt-1 pl-4 border-l border-slate-200 ' . $marginLeft;
                }

                echo "<ul class='{$ulClass}'>";
                foreach ($items as $key => $item) {
                    if (isset($item['children'])) {
                        // It's a dropdown menu
                        $parentKey = is_string($key) ? $key : \Illuminate\Support\Str::camel($item['label']);
                        echo "<li>";
                        echo "<button @click=\"toggleMenu('{$parentKey}')\" :class=\"navButtonClass('{$parentKey}')\" class=\"flex items-center justify-between w-full px-3 py-2.5 text-left rounded-lg font-medium\">";
                        echo "<span class='flex items-center'>";
                        if (isset($item['icon'])) echo $item['icon'];
                        echo $item['label'];
                        echo "</span>";
                        echo "<svg :class=\"{'rotate-90': openMenu.{$parentKey}}\" class='w-4 h-4 transform transition-transform duration-200' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5l7 7-7 7' /></svg>";
                        echo "</button>";
                        echo "<div x-show=\"openMenu.{$parentKey}\" x-collapse>";
                        renderNav($item['children'], $level + 1);
                        echo "</div>";
                        echo "</li>";
                    } else {
                        // It's a direct link. Check if route is '#' or an actual route.
                        $href = $item['route'] === '#' ? '#' : route($item['route']);
                        $routeClassCheck = $item['route'] === '#' ? 'false' : "'{$item['route']}'";

                        echo "<li><a href=\"" . $href . "\" :class=\"navLinkClass({$routeClassCheck})\" class=\"group flex items-center w-full px-3 py-2 text-left rounded-lg\">";
                        echo "<span class='w-1.5 h-1.5 mr-3 rounded-full bg-slate-300 group-hover:bg-sky-500 transition-colors' :class=\"{'bg-sky-500': isRouteActive({$routeClassCheck})}\"></span>";
                        echo $item['label'] . "</a></li>";
                    }
                }
                echo "</ul>";
            }
        @endphp

        {!! renderNav($navItems) !!}

        @auth
            @if(auth()->user()->role === 'admin')
            <div class="pt-4 mt-4 border-t border-slate-200">
                <p class="px-3 text-xs font-semibold tracking-wider text-slate-400 uppercase">Admin</p>
                <a href="{{ route('admin.users.index') }}" :class="navLinkClass('admin.users.index')" class="flex items-center px-3 py-2.5 mt-2 rounded-lg font-medium">
                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
                    User Settings
                </a>
            </div>
            @endif
        @endauth
    </nav>
</aside>

<script>
    function sidebarNav() {
        return {
            openMenu: {},
            routeName: '{{ request()->route()->getName() }}',
            menuMap: {
                'credit.debit.summary': ['credit'], 'credit.debit.transaction': ['credit'], 'credit.debit.report': ['credit'],
                'batch.course': ['office', 'registration'],'student.registration': ['office', 'registration'], 'internship.registration': ['office', 'registration'],
                'student.payment': ['office'], 'students.list': ['office'], 'internship.list': ['office'],
                'staff.summary': ['staff'], 'staff.create': ['staff'], 'staff.salary.list': ['staff'], 'staff.report': ['staff'],
                'offline.category': ['offline'], 'offline.cost.create': ['offline'], 'offline.cost.report': ['offline'],
                'online.cost.create': ['online'], 'online.cost.report': ['online'],
                'loan.create': ['loan'], 'installment.create': ['loan'], 'loan.report': ['loan'],
                'product.summary': ['product'], 'product.category': ['product'], 'product.add': ['product'], 'product.sell': ['product'], 'product.loss': ['product'], 'product.return': ['product'], 
                'product.report': ['product', 'report'], 'product.stock.report': ['product', 'report'], 'product.sell.report': ['product', 'report'], 'product.loss.report': ['product', 'report'], 'product.return.report': ['product', 'report'],
                'company.project.create': ['company'], 'company.project.transaction.add': ['company'], 'company.project.list': ['company'],
                'client.project.create': ['client'], 'client.project.transaction.add': ['client'], 'client.debit.add': ['client'], 'client.project.list': ['client'],
                'target.summary': ['target'], 'revenue.report': ['target'], 'expense.report': ['target'],
                'priority.add': ['priority'], 'priority.list': ['priority'],
                 // Add mappings for new report routes if you create them
                // 'reports.financial.summary': ['reports'], 
                // 'reports.project.client': ['reports'], 
                // 'reports.target.achievement': ['reports'],
            },
            initMenu() {
                const parents = this.menuMap[this.routeName];
                if (parents) {
                    parents.forEach(key => this.openMenu[key] = true);
                }
            },
            toggleMenu(menu) {
                this.openMenu[menu] = !this.openMenu[menu];
            },
            isRouteActive(name) {
                if (name === false) return false;
                return this.routeName === name;
            },
            isParentActive(menuKey) {
                for (const route in this.menuMap) {
                    if (this.menuMap[route].includes(menuKey) && route === this.routeName) {
                        return true;
                    }
                }
                return false;
            },
            navLinkClass(routeName) {
                return this.isRouteActive(routeName)
                    ? 'bg-sky-100 text-sky-600'
                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900';
            },
            navButtonClass(menuKey) {
                return this.isParentActive(menuKey)
                    ? 'text-sky-600'
                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900';
            }
        }
    }
</script>