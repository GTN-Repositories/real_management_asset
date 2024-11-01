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
                    let container = $('#manager-container');
                    container.empty();

                    $.each(data, function(index, manager) {
                        container.append(`
                    <div class="col-md-4 mb-md-0 mb-4">
                        <div class="card border rounded shadow-none" style="height: 30rem;">
                            <div class="card-body">
                                <div class="my-3 pt-2 text-center">
                                <i class="ti ti-building text-primary display-3"></i>
                                </div>
                                <h3 class="card-title text-center text-capitalize mb-1">${manager.name}</h3>
                                <ul class="ps-0 my-4 pt-2 circle-bullets">
                                    ${manager.assets.slice(0, 5).map(
                                        asset => (
                                            `<li class="mb-2 d-flex align-items-center">
                                                            <i class="ti ti-point ti-lg"></i>
                                                            ${asset.name}
                                                        </li>`
                                        )
                                    ).join('')}
                                </ul>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-label-primary d-grid w-100 select-project-btn" data-manager-name="${manager.name}">Pilih Management Project</button>
                            </div>
                        </div>
                    </div>
                `);
                    });

                    $('.select-project-btn').click(function() {
                        let managerName = $(this).data('manager-name');

                        $.ajax({
                            url: "{{ route('driver.selectProject') }}",
                            type: 'POST',
                            data: {
                                manager_name: managerName,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status) {
                                    window.location.href = "{{ route('report-fuel.index') }}";
                                }
                            },
                            error: function() {
                                Swal.fire('Error!',
                                    'An error occurred while selecting the project.',
                                    'error');
                            }
                        });
                    });
                },
                error: function() {
                    Swal.fire('Error!', 'An error occurred while fetching the pricing plans.', 'error');
                }
            });
        });
    </script>
@endpush
