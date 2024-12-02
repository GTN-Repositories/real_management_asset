@extends('layouts.driver')

@section('title', 'Management Project')

@section('content')
    <!-- Pricing Plans -->
    <section class="section-py first-section-pt">
        <div class="container">
            <h2 class="text-center my-4">Management Project</h2>

            <div class="row mx-0 gy-3 px-lg-5" id="manager-container">
                <!-- Cards will be populated here by AJAX -->
            </div>
        </div>
    </section>
    <!--/ Pricing Plans -->
@endsection
@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('driver.data') }}",
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Received data:', data); // Debug log

                    let container = $('#manager-container');
                    container.empty();

                    $.each(data, function(index, manager) {
                        console.log('Processing manager:', manager); // Debug log

                        // Special styling for "All Projects" card
                        const isAllProjects = manager.id === 'all';
                        const cardClass = isAllProjects ? 'border-primary' : 'border';
                        const iconClass = isAllProjects ? 'ti ti-world' : 'ti ti-building';

                        const cardHtml = `
                            <div class="col-md-4 mb-md-0 mb-4">
                                <div class="card ${cardClass} rounded shadow-none" style="height: 32rem;">
                                    <div class="card-body">
                                        <div class="my-3 pt-2 text-center">
                                            <i class="${iconClass} text-primary display-3"></i>
                                        </div>
                                        <h3 class="card-title text-center text-capitalize mb-1">
                                            ${manager.name}
                                            ${isAllProjects ? ' <span class="badge bg-primary">Super Admin</span>' : ''}
                                        </h3>
                                        ${isAllProjects ?
                                            '<p class="text-center text-muted">Access and manage all projects</p>' : ''
                                        }
                                        <ul class="ps-0 my-4 pt-2 circle-bullets">
                                            ${manager.assets && manager.assets.length > 0 ?
                                                manager.assets.slice(0, 5).map(
                                                    asset => `
                                                        <li class="mb-2 d-flex align-items-center">
                                                            <i class="ti ti-point ti-lg"></i>
                                                            ${asset.name}
                                                        </li>`
                                                ).join('') :
                                                '<li class="text-muted">No assets available</li>'
                                            }
                                        </ul>
                                    </div>
                                    <div class="card-footer">
                                        <button class="btn ${isAllProjects ? 'btn-primary' : 'btn-label-primary'} d-grid w-100 select-project-btn"
                                                data-project-id="${manager.id}">
                                            ${isAllProjects ? 'View All Projects' : 'Pilih Management Project'}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;

                        console.log('Adding card HTML:', cardHtml); // Debug log
                        container.append(cardHtml);
                    });

                    $('.select-project-btn').click(function() {
                        let projectId = $(this).data('project-id');
                        console.log('Selected project ID:', projectId); // Debug log

                        $.ajax({
                            url: "{{ route('driver.selectProject') }}",
                            type: 'POST',
                            data: {
                                project_id: projectId,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                console.log('Select project response:', response); // Debug log
                                if (response.status) {
                                    window.location.href = "{{ route('report-fuel.index') }}";
                                }
                            },
                            error: function(error) {
                                console.error('Select project error:', error); // Debug log
                                Swal.fire('Error!',
                                    'An error occurred while selecting the project.',
                                    'error');
                            }
                        });
                    });
                },
                error: function(error) {
                    console.error('Fetch data error:', error); // Debug log
                    Swal.fire('Error!', 'An error occurred while fetching the management project.', 'error');
                }
            });
        });
    </script>
@endpush
