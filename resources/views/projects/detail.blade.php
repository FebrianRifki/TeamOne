@extends('layouts.template.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row g-4 text-center">

            {{-- Column --}}
            @foreach ([['title' => 'To Do', 'color' => '#007bff'], ['title' => 'In Progress', 'color' => '#17a2b8'], ['title' => 'Done', 'color' => '#28a745']] as $col)
                <div class="col-md">
                    <div class="bg-white border rounded shadow-sm h-100 d-flex flex-column">

                        {{-- Header --}}
                        <div class="p-2 rounded-top" style="background-color: {{ $col['color'] }}">
                            <span class="text-white fw-semibold">{{ $col['title'] }}</span>
                        </div>

                        {{-- Scrollable body --}}
                        <div class="flex-grow-1 p-3 overflow-auto" style="max-height: 800px; background-color: #f8f9fa;">

                            @foreach (range(1, 3) as $i)
                                <div class="card border-0 shadow-sm mb-3 hover-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="card-title fw-bold mb-2 text-primary">Task {{ $i }}</h6>
                                            <button class="btn btn-light btn-sm border-0 text-secondary">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                        </div>
                                        <p class="card-text text-muted small mb-0">
                                            Some quick example text to build on the card title and make up the bulk of the
                                            card's content.
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>

    {{-- Optional hover styling --}}
    @push('styles')
        <style>
            .hover-card {
                transition: all 0.2s ease;
            }

            .hover-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
            }
        </style>
    @endpush
@endsection
