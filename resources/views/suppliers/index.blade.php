@extends('layouts.modernize')

@section('content')
    <div class="container px-4 py-8 mx-auto">
        <div class="flex flex-col justify-between gap-4 mb-8 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Suppliers</h1>
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                <i class="mr-2 ti ti-plus"></i> Add Supplier
            </a>
        </div>

        <div class="overflow-hidden bg-white rounded-xl shadow">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Show</label>
                        <select id="supplierTable_length" class="select select-bordered select-sm w-20">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label class="text-sm font-medium text-gray-700">entries</label>
                    </div>
                    <div class="w-full sm:w-64">
                        <input type="search" id="supplierSearch" placeholder="Search suppliers..."
                            class="input input-bordered input-sm w-full">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="supplierTable" class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                <a href="#" class="cursor-pointer hover:text-gray-800">Name</a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                <a href="#" class="cursor-pointer hover:text-gray-800">Email</a>
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>

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
            #supplierTable tbody tr:hover {
                background-color: #f9fafb;
            }

            #supplierTable tbody td {
                padding: 1rem 1.5rem;
                font-size: 0.875rem;
                color: #374151;
            }

            .dt-loading {
                text-align: center;
                padding: 2rem;
                color: #6b7280;
            }

            .page-link {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 2.5rem;
                height: 2.5rem;
                padding: 0.5rem;
                border: 1px solid #d1d5db;
                border-radius: 0.375rem;
                background-color: #ffffff;
                color: #374151;
                font-size: 0.875rem;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .page-link:hover:not(.disabled) {
                background-color: #f3f4f6;
                border-color: #9ca3af;
            }

            .page-item.active .page-link {
                background-color: #5d87ff;
                border-color: #5d87ff;
                color: white;
            }

            .page-item.disabled .page-link {
                opacity: 0.5;
                cursor: not-allowed;
            }
        </style>

        <script>
            $(function() {
                let dataTable = $('#supplierTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    searching: false,
                    lengthChange: false,
                    ajax: {
                        url: '{{ route('suppliers.datatables') }}',
                        data: function(d) {
                            d.search = {
                                value: $('#supplierSearch').val()
                            };
                        }
                    },
                    columns: [{
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false,
                            className: 'text-right'
                        }
                    ],
                    pageLength: 10,
                    bInfo: false,
                    bPaginate: false,
                    bFilter: false,
                    drawCallback: function(settings) {
                        updatePagination();
                        updateTableInfo();
                    },
                    createdRow: function(row, data, index) {
                        $(row).addClass('border-b border-gray-200 hover:bg-gray-50 transition-colors');
                    }
                });

                // Search
                $('#supplierSearch').on('keyup', function() {
                    dataTable.draw();
                });

                // Length change
                $('#supplierTable_length').on('change', function() {
                    dataTable.page.len($(this).val()).draw();
                });

                // Update pagination
                function updatePagination() {
                    let pageInfo = dataTable.page.info();
                    let pagination = $('#pagination');
                    pagination.empty();

                    // Previous button
                    let prevBtn = $('<button>')
                        .addClass('page-link')
                        .attr('data-page', pageInfo.page - 1)
                        .html('<i class="ti ti-chevron-left"></i>')
                        .on('click', function(e) {
                            e.preventDefault();
                            if (pageInfo.page > 0) {
                                dataTable.page(pageInfo.page - 1).draw(false);
                            }
                        });

                    if (pageInfo.page === 0) {
                        prevBtn.addClass('disabled');
                    }
                    pagination.append(prevBtn);

                    // Page buttons
                    let startPage = Math.max(0, pageInfo.page - 2);
                    let endPage = Math.min(pageInfo.pages, pageInfo.page + 3);

                    if (startPage > 0) {
                        let firstBtn = $('<button>')
                            .addClass('page-link')
                            .text('1')
                            .on('click', function(e) {
                                e.preventDefault();
                                dataTable.page(0).draw(false);
                            });
                        pagination.append(firstBtn);

                        if (startPage > 1) {
                            pagination.append($('<span>').addClass('px-2 text-gray-400').text('...'));
                        }
                    }

                    for (let i = startPage; i < endPage; i++) {
                        let btn = $('<button>')
                            .addClass('page-link')
                            .text(i + 1)
                            .on('click', function(e) {
                                e.preventDefault();
                                dataTable.page(i).draw(false);
                            });

                        if (i === pageInfo.page) {
                            btn.addClass('active');
                        }
                        pagination.append(btn);
                    }

                    if (endPage < pageInfo.pages) {
                        if (endPage < pageInfo.pages - 1) {
                            pagination.append($('<span>').addClass('px-2 text-gray-400').text('...'));
                        }

                        let lastBtn = $('<button>')
                            .addClass('page-link')
                            .text(pageInfo.pages)
                            .on('click', function(e) {
                                e.preventDefault();
                                dataTable.page(pageInfo.pages - 1).draw(false);
                            });
                        pagination.append(lastBtn);
                    }

                    // Next button
                    let nextBtn = $('<button>')
                        .addClass('page-link')
                        .attr('data-page', pageInfo.page + 1)
                        .html('<i class="ti ti-chevron-right"></i>')
                        .on('click', function(e) {
                            e.preventDefault();
                            if (pageInfo.page + 1 < pageInfo.pages) {
                                dataTable.page(pageInfo.page + 1).draw(false);
                            }
                        });

                    if (pageInfo.page + 1 >= pageInfo.pages) {
                        nextBtn.addClass('disabled');
                    }
                    pagination.append(nextBtn);
                }

                // Update table info
                function updateTableInfo() {
                    let pageInfo = dataTable.page.info();
                    let start = pageInfo.start + 1;
                    let end = pageInfo.end;
                    let total = pageInfo.recordsFiltered;
                    $('#tableInfo').text(`${start} to ${end} of ${total}`);
                }
            });
        </script>
    @endpush
@endsection
