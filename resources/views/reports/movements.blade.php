@extends('layouts.modernize')

@section('content')
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a></li>
                        <li><i class="ti ti-chevron-right text-xs"></i></li>
                        <li><a href="#" class="hover:text-blue-600">Reports</a></li>
                        <li><i class="ti ti-chevron-right text-xs"></i></li>
                        <li class="text-gray-800 font-medium">Stock Movements</li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-bold text-gray-900">Stock Movements</h2>
                <p class="text-sm text-gray-500 mt-1">History of all inventory transactions</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Movements</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2" id="total-movements">0</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="ti ti-arrows-exchange text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Stock In Entries</p>
                        <p class="text-3xl font-bold text-green-600 mt-2" id="in-count">0</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="ti ti-arrow-down-left text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Stock Out Entries</p>
                        <p class="text-3xl font-bold text-orange-600 mt-2" id="out-count">0</p>
                    </div>
                    <div class="bg-orange-100 rounded-full p-3">
                        <i class="ti ti-arrow-up-right text-orange-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="ti ti-history mr-2 text-blue-600"></i>
                    Movement History
                </h3>
                <div class="flex gap-2">
                    <select id="type-filter" class="px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="STOCK_IN">Stock In</option>
                        <option value="STOCK_OUT">Stock Out</option>
                        <option value="ADJUSTMENT">Adjustment</option>
                        <option value="TRANSFER_IN">Transfer In</option>
                        <option value="TRANSFER_OUT">Transfer Out</option>
                    </select>
                    <button onclick="refreshTable()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="ti ti-refresh mr-2"></i> Refresh
                    </button>
                </div>
            </div>

            <!-- Custom DataTables Controls -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Show</label>
                        <select id="reportTable_length" class="select select-bordered select-sm w-20">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label class="text-sm font-medium text-gray-700">entries</label>
                    </div>
                    <div class="w-full sm:w-64">
                        <input type="search" id="reportSearch" placeholder="Search movements..."
                            class="input input-bordered input-sm w-full">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="report-table" class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Warehouse</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Document</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>

            <!-- Custom Pagination Info -->
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between bg-gray-50">
                <div class="text-sm text-gray-600">
                    Showing <span id="tableInfo"></span> entries
                </div>
                <div class="flex gap-2" id="pagination"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <style>
        #report-table tbody td { padding: 1rem 1.5rem; font-size: 0.875rem; color: #374151; }
        .page-link {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 2.5rem; height: 2.5rem; padding: 0.5rem;
            border: 1px solid #d1d5db; border-radius: 0.375rem;
            background-color: #ffffff; color: #374151;
            font-size: 0.875rem; cursor: pointer; transition: all 0.2s ease;
        }
        .page-link:hover:not(.disabled) { background-color: #f3f4f6; border-color: #9ca3af; }
        .page-item.active .page-link, .page-link.active { background-color: #5d87ff; border-color: #5d87ff; color: white; }
        .page-link.disabled { opacity: 0.5; cursor: not-allowed; }
    </style>
    <script>
        $(document).ready(function() {
            const table = $('#report-table').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                lengthChange: false,
                bInfo: false,
                bPaginate: false,
                ajax: {
                    url: "{{ route('reports.movements') }}",
                    data: function(d) {
                        d.type = $('#type-filter').val();
                        d.search = { value: $('#reportSearch').val() };
                    }
                },
                columns: [
                    { data: 'date', name: 'created_at' },
                    { data: 'product', name: 'product' },
                    { data: 'warehouse', name: 'warehouse' },
                    { data: 'type', name: 'type', className: 'text-center' },
                    { data: 'quantity', name: 'quantity', className: 'text-right' },
                    { data: 'doc', name: 'document_number' },
                    { data: 'user', name: 'user' }
                ],
                order: [[0, 'desc']],
                drawCallback: function() {
                    updateSummary();
                    updatePagination();
                    updateTableInfo();
                }
            });

            $('#type-filter').on('change', function() { table.draw(); });
            $('#reportSearch').on('keyup', function() { table.draw(); });
            $('#reportTable_length').on('change', function() { table.page.len($(this).val()).draw(); });

            window.refreshTable = function() { table.ajax.reload(); };

            function updatePagination() {
                let pageInfo = table.page.info();
                let pagination = $('#pagination');
                pagination.empty();

                let prevBtn = $('<button>').addClass('page-link').html('<i class="ti ti-chevron-left"></i>').on('click', function(e) {
                    e.preventDefault(); if (pageInfo.page > 0) table.page(pageInfo.page - 1).draw(false);
                });
                if (pageInfo.page === 0) prevBtn.addClass('disabled');
                pagination.append(prevBtn);

                let startPage = Math.max(0, pageInfo.page - 2);
                let endPage = Math.min(pageInfo.pages, pageInfo.page + 3);

                for (let i = startPage; i < endPage; i++) {
                    let btn = $('<button>').addClass('page-link').text(i + 1).on('click', function(e) {
                        e.preventDefault(); table.page(i).draw(false);
                    });
                    if (i === pageInfo.page) btn.addClass('active');
                    pagination.append(btn);
                }

                let nextBtn = $('<button>').addClass('page-link').html('<i class="ti ti-chevron-right"></i>').on('click', function(e) {
                    e.preventDefault(); if (pageInfo.page + 1 < pageInfo.pages) table.page(pageInfo.page + 1).draw(false);
                });
                if (pageInfo.page + 1 >= pageInfo.pages) nextBtn.addClass('disabled');
                pagination.append(nextBtn);
            }

            function updateTableInfo() {
                let pageInfo = table.page.info();
                $('#tableInfo').text(`${pageInfo.start + 1} to ${pageInfo.end} of ${pageInfo.recordsFiltered}`);
            }

            function updateSummary() {
                $.get("{{ route('reports.movements') }}", { summary: true }, function(data) {
                    if (data.summary) {
                        $('#total-movements').text(data.summary.total_movements || 0);
                        $('#in-count').text(data.summary.in_count || 0);
                        $('#out-count').text(data.summary.out_count || 0);
                    }
                });
            }
        });
    </script>
    @endpush
@endsection
