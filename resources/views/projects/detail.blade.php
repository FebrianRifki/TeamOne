@extends('layouts.template.app')

@section('content')

<style>
    .task-container {
        min-height: 100px;
    }

    /* Saat dragging, kasih gaya biar placeholder gak bikin kolom collapse */
    .task-placeholder {
        background-color: #e9ecef;
        border: 2px dashed #adb5bd;
        border-radius: 8px;
        margin-bottom: 12px;
        height: 80px;
    }

    /* Biar card tetap smooth saat di-drag */
    .ui-sortable-helper {
        transform: rotate(2deg);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-2">
        <div id="projectId" class="d-none">{{ $project->id }}</div>
        <button class="btn-sm btn-primary mr-2" data-toggle="modal" data-target="#addTaskModal">Add Task</button>
        <div>
            <button class="btn-sm btn-primary mr-2" onclick="editProject()">Edit Project</button>
            <button class="btn-sm btn-danger" onclick="deleteProject()">Delete Project</button>
        </div>
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
            <div class=" bg-white border rounded shadow-sm h-100 d-flex flex-column">
                {{-- Header --}}
                <div class="p-2 rounded-top" style="background-color: {{ $col['color'] }}">
                    <span class="text-white fw-semibold">{{ $col['title'] }}</span>
                </div>

                {{-- Scrollable body --}}
                <div class="task-container flex-grow-1 p-3 overflow-y-auto overflow-x-hidden" data-status="{{ $col['status'] }}" style="max-height: 800px; background-color: #f8f9fa;">
                    @forelse ($project->tasks->where('status', $col['status']) as $task)
                    <div class="card border-0 shadow-sm hover-card mb-3" data-id="{{ $task->id }}">
                        <div class="card-body tasks">
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
                            <p class="card-text text-muted small mb-0" style="text-align: left;">
                                {{ $task->description }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <p class="no-task text-muted small mt-3">No tasks in this column.</p>
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

<div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Task</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body" id="addTaskModalBody">
                <form id="addTaskForm" action="{{ route('tasks.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="task_id" name="task_id">
                    <input type="hidden" id="project_id" name="project_id" value="{{ $project->id }}">

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

                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary" type="submit">Save</button>
                    </div>
                </form>
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
    $(document).ready(function() {
        $(".task-container").sortable({
            connectWith: ".task-container",
            placeholder: "task-placeholder",
            items: ".card",
            receive: function(event, ui) {
                const container = $(this);
                container.find(".no-task").remove();

                const taskId = ui.item.data("id");
                const newStatus = $(this).data("status");
                $.ajax({
                    url: `/tasks/status/${taskId}`,
                    type: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        status: newStatus
                    },
                    success: function(response) {
                        console.log(response);
                    },
                    error: function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update task status.',
                        });
                    }
                });
            },
            update: function(event, ui) {
                const body = $(this);
                if (body.find(".card").length === 0) {
                    if (body.find(".no-task").length === 0) {
                        body.append("<p class='no-task text-muted small mt-3'>No tasks in this column.</p>");
                    }
                }
            }
        }).disableSelection();
    });

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

    function editProject() {
        console.log("edit project");
        var id = $("#projectId").text();
        location.href = `/projects/${id}/edit`;
    }

    function deleteProject() {
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