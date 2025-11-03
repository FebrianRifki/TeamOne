@extends('layouts.template.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Project</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('projects.update', $project->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="project_name">Project Name</label>
                                <input type="text" class="form-control" id="project_name" name="project_name"
                                    value="{{ $project->project_name }}">
                            </div>
                            <div class="form-group">
                                <label for="project_description">Project Description</label>
                                <textarea class="form-control" id="project_description" name="project_description">{{ $project->description }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
