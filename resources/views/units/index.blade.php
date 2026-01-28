@extends('layouts.modernize')

@section('content')
    <div class="container px-4 py-8 mx-auto">
        <div class="flex flex-col justify-between gap-4 mb-8 sm:flex-row sm:items-center">
            <h1 class="text-3xl font-bold text-gray-900">Units</h1>
            <a href="{{ route('units.create') }}" class="btn btn-primary">
                <i class="mr-2 ti ti-plus"></i> Add Unit
            </a>
        </div>

        <div class="overflow-hidden bg-white shadow rounded-xl">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Show</label>
                        <select id="unitTable_length" class="w-20 select select-bordered select-sm">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label class="text-sm font-medium text-gray-700">entries</label>
                    </div>
                    <div class="w-full sm:w-64">
                        <input type="search" id="unitSearch" placeholder="Search units..."
                            class="w-full input input-bordered input-sm">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="unitTable" class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-6 py-3 text-xs font-medium text-left text-gray-600 uppercase">
                                Code
                            </th>
                            <th class="px-6 py-3 text-xs font-medium text-left text-gray-600 uppercase">
                                Name
                            </th>
                            <th class="px-6 py-3 text-xs font-medium text-left text-gray-600 uppercase">
                                Symbol
                            </th>
                            <th class="px-6 py-3 text-xs font-medium text-left text-gray-600 uppercase">
                                Description
                            </th>
                            <th class="px-6 py-3 text-xs font-medium text-right text-gray-600 uppercase">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50">
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
            #unitTable tbody tr:hover {
                background-color: #f9fafb;
            }

            #unitTable tbody td {
                padding: 1rem 1.5rem;
                font-size: 0.875rem;
                color: #374151;
            }
        </style>

        <script>
            $(function() {
                let dataTable = $('#unitTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    searching: false,
                    lengthChange: false,
                    ajax: {
                        url: '{{ route('units.datatables') }}',
                        data: function(d) {
                            d.search = {
                                value: $('#unitSearch').val()
                            };
                        }
                    },
                    columns: [{
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'symbol',
                            name: 'symbol'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    pageLength: 10,
                    bInfo: false,
                    bPaginate: false,
                    bFilter: false,
                    drawCallback: function() {
                        updatePagination();
                        updateTableInfo();
                    }
                });

                // Search
                $('#unitSearch').on('keyup', function() {
                    dataTable.draw();
                });

                // Length change
                $('#unitTable_length').on('change', function() {
                    dataTable.page.len($(this).val()).draw();
                });

                function updatePagination() {
                    let pageInfo = dataTable.page.info();
                    let pagination = $('#pagination');
                    pagination.empty();

                    let prevBtn = $('<button>')
                        .addClass('page-link')
                        .html('<i class="ti ti-chevron-left"></i>')
                        .on('click', function() {
                            if (pageInfo.page > 0) {
                                dataTable.page(pageInfo.page - 1).draw(false);
                            }
                        });

                    if (pageInfo.page === 0) prevBtn.addClass('disabled');
                    pagination.append(prevBtn);

                    for (let i = 0; i < pageInfo.pages; i++) {
                        let btn = $('<button>')
                            .addClass('page-link')
                            .text(i + 1)
                            .on('click', function() {
                                dataTable.page(i).draw(false);
                            });

                        if (i === pageInfo.page) btn.addClass('active');
                        pagination.append(btn);
                    }

                    let nextBtn = $('<button>')
                        .addClass('page-link')
                        .html('<i class="ti ti-chevron-right"></i>')
                        .on('click', function() {
                            if (pageInfo.page + 1 < pageInfo.pages) {
                                dataTable.page(pageInfo.page + 1).draw(false);
                            }
                        });

                    if (pageInfo.page + 1 >= pageInfo.pages) nextBtn.addClass('disabled');
                    pagination.append(nextBtn);
                }

                function updateTableInfo() {
                    let info = dataTable.page.info();
                    $('#tableInfo').text(`${info.start + 1} to ${info.end} of ${info.recordsFiltered}`);
                }
            });
        </script>
    @endpush
@endsection
