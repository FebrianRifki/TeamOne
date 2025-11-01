@extends('layouts.template.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row g-4 text-center">
        @php
        $columns = [
        ['status' => 'todo', 'title' => 'To Do', 'color' => '#007bff'],
        ['status' => 'in_progress', 'title' => 'In Progress', 'color' => '#17a2b8'],
        ['status' => 'done', 'title' => 'Done', 'color' => '#28a745'],
        ];
        @endphp

        @foreach ($columns as $col)
        <div class="col-md">
            <div class="bg-white border rounded shadow-sm h-100 d-flex flex-column">
                {{-- Header --}}
                <div class="p-2 rounded-top" style="background-color: {{ $col['color'] }}">
                    <span class="text-white fw-semibold">{{ $col['title'] }}</span>
                </div>

                {{-- Scrollable body --}}
                <div class="flex-grow-1 p-3 overflow-auto" style="max-height: 800px; background-color: #f8f9fa;">
                    @forelse ($project->tasks->where('status', $col['status']) as $task)
                    <div class="card border-0 shadow-sm mb-3 hover-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="card-title fw-bold mb-2 text-primary">
                                    {{ $task->task_name }}
                                </h6>
                                <button class="btn btn-light btn-sm border-0 text-secondary" onclick="displayEditModal({{$task->id}})">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                            <p class="card-text text-muted small mb-0">
                                {{ $task->description }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted small mt-3">No tasks in this column.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Task</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="editModalBody">
                <div class="modal-body">
                    <form id="editTaskForm">
                        <div class="form-group">
                            <label>Task Name</label>
                            <input type="text" id="task_name" name="task_name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea id="description" name="description" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Assigned To</label>
                            <select id="user_id" name="user_id" class="form-control">
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Priority</label>
                            <select id="priority" name="priority" class="form-control">
                                <option value="1">Low</option>
                                <option value="2">Medium</option>
                                <option value="3">High</option>
                            </select>
                        </div>
                    </form>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="button" data-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>

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

@push('scripts')
<script>
    function displayEditModal(id) {
        $('#editModal').modal('show');
        getDetailTask(id);
    }

    function getDetailTask(id) {
        $.ajax({
            url: `/projects/${id}/edit`,
            type: 'GET',
            success: function(response) {
                $('#task_name').val(response[0].task_name);
                $('#description').val(response[0].description);

                // Kosongkan select dan append options
                var userSelect = $('#user_id');
                userSelect.empty();

                response[1].forEach(u => {
                    // Buat option
                    var option = $('<option></option>')
                        .val(u.id)
                        .text(u.name);

                    // Jika ini user yang di-assign, tambahkan selected
                    if (u.id === response[0].user_id) {
                        option.prop('selected', true);
                    }

                    userSelect.append(option);
                });

                $('#priority').val(response[0].priority);
            },
            error: function(error) {
                console.log(error);
            }
        });
    }
</script>
@endpush
@endsection