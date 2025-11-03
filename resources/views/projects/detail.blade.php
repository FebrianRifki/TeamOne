@extends('layouts.template.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-end mb-2">
        <div id="projectId" class="d-none">{{ $project->id }}</div>
        <button class="btn-sm btn-danger" onclick="deleteProject()">Delete Project</button>
    </div>
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
                                <div>
                                    <button class="btn btn-light btn-sm border-0 text-secondary" onclick="displayEditModal({{$task->id}})">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn btn-light btn-sm border-0 text-secondary" onclick="deleteTask({{$task->id}})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
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
                    <input type="hidden" id="task_id" name="task_id">
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
                            <select id="assigned_to" name="assigned_to" class="form-control">
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
            <div class="modal-footer ">
                <div>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" id="buttonUpdate" type="button" onclick="updateTask()">Save</button>
                </div>
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
        $('#task_id').val(id);
        $('#editModal').modal('show');
        getDetailTask(id);
    }

    function getDetailTask(id) {
        $.ajax({
            url: `/tasks/${id}/edit`,
            type: 'GET',
            success: function(response) {
                $('#task_name').val(response[0].task_name);
                $('#description').val(response[0].description);

                var userSelect = $('#assigned_to');
                userSelect.empty();

                response[1].forEach(u => {

                    var option = $('<option></option>')
                        .val(u.id)
                        .text(u.name);

                    if (u.id === response[0].assigned_to) {
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

    function updateTask() {
        var id = $('#task_id').val();
        $('#buttonUpdate').prop('disabled', true);
        $.ajax({
            url: `/tasks/${id}`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'PUT',
            data: {
                task_name: $('#task_name').val(),
                description: $('#description').val(),
                assigned_to: $('#assigned_to').val(),
                priority: $('#priority').val(),
            },
            success: function(response) {
                $('#editModal').modal('hide');
                $('#task_name').val('');
                $('#description').val('');
                $('#assigned_to').val('');
                $('#priority').val('');

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Task updated successfully!',
                    timer: 1000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
                $('#buttonUpdate').prop('disabled', false);
            },
            error: function(error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update task!',
                    timer: 1000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        })
    }

    function deleteTask(id) {
        var taskId = id;
        Swal.fire({
            title: 'Are you sure?',
            text: "This task will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/tasks/${taskId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Task has been deleted.',
                            timer: 1000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete task!',
                            timer: 1000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
    }

    function deleteProject(){
        var id = $("#projectId").text();

        Swal.fire({
            title: 'Are you sure?',
            text: "This project will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/projects/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Project has been deleted.',
                            timer: 1000,
                            showConfirmButton: false
                        }).then(() => {
                            location.href = '/dashboard';
                        });
                    },
                    error: function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete project!',
                            timer: 1000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        })
        
    }
</script>
@endpush
@endsection