@extends('layouts.global')

@section('title', 'Inspeksi')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">{{ $schedule->type }} {{ $schedule->name }} - {{ $schedule->unit->name }} / <span
                class="text-muted">{{ $questionCount }} pertanyaan</span></h4>

{{-- @dd($questionsGroupedByCategory) --}}
        <!-- Product List Table -->
        <div class="card">
            <div class="card-body">
                <div class="accordion mt-3" id="accordionExample">
                    @php
                        $no = 1;
                    @endphp
                    @foreach ($questionsGroupedByCategory as $categoryId => $questions)
                        <div class="card accordion-item">
                            <h2 class="accordion-header" id="heading{{ $categoryId }}">
                                <button type="button" class="accordion-button bg-primary text-white"
                                    data-bs-toggle="collapse" data-bs-target="#accordion{{ $categoryId }}"
                                    aria-expanded="false" aria-controls="accordion{{ $categoryId }}">
                                    Kategori {{ $no++ }} ({{ $questions->first()->categoryFrom->name ?? 'Tidak ada kategori' }})
                                </button>
                            </h2>

                            <div id="accordion{{ $categoryId }}" class="accordion-collapse collapse"
                                aria-labelledby="heading{{ $categoryId }}" data-bs-parent="#questionsAccordion">
                                <div class="accordion-body">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>PERTANYAAN</th>
                                                <th>PASS</th>
                                                <th>FAIL</th>
                                                <th>BACKLOG</th>
                                                <th>KETERANGAN</th>
                                                <th>AKSI</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            @foreach ($questions as $question)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $question->question }}</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <button type="button"
                                                            class="btn rounded-pill btn-icon btn-outline-secondary">
                                                            <span class="ti ti-eye"></span>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-3">
            <button type="button" class="btn btn-secondary waves-effect waves-light me-1" data-bs-dismiss="modal">Kembali</button>
            <form action="">
                <input type="hidden" name="id" value="{{ $schedule->id }}">
                <button type="submit" class="btn btn-primary btn-next waves-effect waves-light">Selesai</button>
            </form>
        </div>
    </div>
    <div class="modal fade" id="modal-ce" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-pricing">
            <div class="modal-content p-2 p-md-5">
                <div class="modal-body" id="content-modal-ce">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        
    </script>
@endpush
