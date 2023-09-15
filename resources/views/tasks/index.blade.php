<!-- resources/views/tasks/index.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Task List</h1>

        <div class="card">
            <!-- Task Form -->
            <form id="task-form">
                <div class="mb-2">
                    <label class="containers">Show All Task
                        <input type="checkbox" id="show-all-tasks">
                        <span class="checkmark"></span>
                    </label>
                </div>
                @csrf
                <div style="display: flex; align-items: center;">
                    <div class="form-group" style="flex-grow: 1; margin-right: 10px;">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupPrepend2"><i class="fa fa-tasks"
                                        style="font-size: 25px;"></i></span>
                            </div>
                            <input type="text" id="task-name" class="form-control" placeholder="Add a new task">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Task </button>

                </div>
            </form>


            <div id="error-message" class="alert alert-danger" style="display: none;"></div>

            <!-- Task Table -->
            <table class="table table-bordered ">
                <thead>
                    {{-- <tr>
                        <th>Task</th>
                        <th>Action</th>
                    </tr> --}}
                </thead>
                <tbody id="task-list">
                    @foreach ($tasks as $task)
                        @include('partials.task', ['task' => $task])
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
    <script>
        $(document).ready(function() {
            setTime();
            // AJAX for adding tasks without page reload
            $('#task-form').submit(function(event) {
                event.preventDefault();

                let taskName = $('#task-name').val();
                if (!taskName) {
                    $('#error-message').text("Please Enter Task Name...");
                    $('#error-message').show();
                    return false;
                }else{
                    $('#error-message').text("");
                    $('#error-message').hide();
                }
                $.ajax({
                    type: 'POST',
                    url: '/tasks',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'name': taskName,
                    },
                    success: function(response) {
                        console.log(response);
                        // This block won't be executed for validation errors
                        $('#task-list').append(response.taskHtml);
                        $('#task-name').val('');
                        $('#error-message').hide();
                        setTime();
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 422) {
                            // Handle validation errors
                            let response = JSON.parse(xhr.responseText);
                            if (response.errors && response.errors.name) {
                                // Display the validation error message
                                $('#error-message').text(response.errors.name[0]);
                                $('#error-message').show();
                            }
                        } else {
                            // Handle other types of errors
                            console.error(error);
                        }
                    }
                });
            });



            // AJAX for marking tasks as completed
            $(document).on('change', '.task-checkbox', function() {
                var checkbox = $(this);
                let taskId = checkbox.data('task-id');
                let isCompleted = checkbox.prop('checked');

                $.ajax({
                    type: 'PATCH',
                    url: '/tasks/' + taskId,
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'completed': isCompleted
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update the UI based on the response
                            let taskItem = checkbox.closest('.task-item');
                            if (isCompleted) {
                                // If the task was marked as completed
                                taskItem.addClass('completed');
                            } else {
                                // If the task was marked as not completed
                                taskItem.removeClass('completed');
                            }
                        } else {
                            // Handle any error messages or conditions here
                            console.error(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle any AJAX errors here
                        console.error(error);
                    }
                });
            });




            // AJAX for deleting tasks with confirmation
            $(document).on('click', '.delete-task', function() {
                if (confirm("Are you sure to delete this task?")) {
                    let taskId = $(this).data('task-id');
                    $.ajax({
                        type: 'DELETE',
                        url: '/tasks/' + taskId,
                        data: {
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Remove the task from the list
                                $(this).closest('tr').fadeOut(500, function() {
                                    $(this).remove();
                                });

                            } else {
                                // Handle any error messages or conditions here
                                console.error(response.message);
                            }
                        }.bind(this)
                    });
                }
            });
            $('#show-all-tasks').on('change', function() {
                // Check if the checkbox is checked
                if ($(this).prop('checked')) {
                    // If checked, set the opacity of all completed tasks to 1
                    $('.task-item.completed').css('opacity', 1);
                } else {
                    // If unchecked, set the opacity of completed tasks back to 0.2
                    $('.task-item.completed').css('opacity', 0.2);
                }
            });


        });
        // Update timestamps 
        function setTime() {
            let timestamps = document.querySelectorAll(".timestamp");
            timestamps.forEach(function(timestampElement) {
                let timestamp = moment(timestampElement.getAttribute("data-timestamp"));
                let timeAgo = timestamp.fromNow();
                timestampElement.innerText = timeAgo;
            });
        };
    </script>
@endsection
