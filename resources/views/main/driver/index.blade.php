@extends('layouts.driver')

@section('title', 'Management Project')

@push('css')
    <style>
        .card-project {
            border-radius: 39px;
            max-width: 356px;
            background-color: #f4f3fa;
        }

        .border-logo {
            border-radius: 100%;
            box-shadow: 0 8px 4px rgba(165, 163, 174, 0.3);
            width: 100px;
            height: 100px;
            margin: auto;
            background-color: #FFFFFF;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .list-scroll {
            overflow-y: scroll;
        }

        .card-footer {
            display: flex;
            justify-content: center;
        }

        .select-project-btn {
            max-width: 250px;
        }
    </style>
@endpush

@section('content')
    <!-- Pricing Plans -->
    <section class="section-py first-section-pt">
        <div class="container">
            <h1 class="text-center fw-bold text-black my-4">Management Project</h1>

            <div class="row mx-0 gy-3 px-lg-5 justify-content-start" id="manager-container">
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
                url: "{{ route('select-project.data') }}",
                type: 'GET',
                dataType: 'json',
                success: function(data) {

                    let container = $('#manager-container');
                    container.empty();

                    $.each(data, function(index, manager) {

                        // Special styling for "All Projects" card
                        const isAllProjects = manager.id === 'all';
                        const cardClass = isAllProjects ? 'border-primary' : 'border';
                        const iconClass = isAllProjects ? '{{ asset('images/globe.png') }}' :
                            '{{ asset('images/corporate.png') }}';

                        const cardHtml = `
                            <div class="col-md-4 mb-md-0 mb-4">
                                <div class="card ${cardClass} card-project shadow-lg" style="height: 32rem;">
                                    <div class="card-body">
                                        <div class="my-3 text-center border-logo">
                                            <img src="${iconClass}" alt="" width="70">
                                        </div>
                                        <h3 class="card-title text-center text-capitalize fw-bold mb-1">
                                            ${manager.name}
                                        </h3>
                                        ${isAllProjects ?
                                            '<p class="text-center fw-bold">Access and manage all projects</p>' : '<p class="text-center fw-bold">Access and manage projects</p>'
                                        }
                                        <hr>
                                        <ul class="ps-0 my-4 pt-2 circle-bullets list-scroll" style="height: 150px;">
                                            ${manager.assets && manager.assets.length > 0 ?
                                                manager.assets.slice(0, 5).map(
                                                    asset => `
                                                            <li class="mb-2 d-flex align-items-center">
                                                                <i class="ti ti-point ti-lg"></i>
                                                                AST - ${asset.ids} - ${asset.name}
                                                            </li>`
                                                ).join('') :
                                                '<li class="text-muted">No assets available</li>'
                                            }
                                        </ul>
                                    </div>
                                    <div class="card-footer">
                                        <button class="btn ${isAllProjects ? 'btn-primary' : 'btn-outline-primary'} d-grid w-100 select-project-btn"
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
                            url: "{{ route('select-project.selectProject') }}",
                            type: 'POST',
                            data: {
                                project_id: projectId,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                console.log('Select project response:',
                                    response); // Debug log
                                if (response.status) {
                                    window.location.href =
                                        "{{ route('report-fuel.index') }}";
                                }
                            },
                            error: function(error) {
                                console.error('Select project error:',
                                    error); // Debug log
                                Swal.fire('Error!',
                                    'An error occurred while selecting the project.',
                                    'error');
                            }
                        });
                    });
                },
                error: function(error) {
                    console.error('Fetch data error:', error); // Debug log
                    Swal.fire('Error!', 'An error occurred while fetching the management project.',
                        'error');
                }
            });
        });
    </script>
@endpush
