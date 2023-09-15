<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|unique:tasks|max:255',
        ], [
            'name' => 'A task with this name already exists.',
        ]);

        $task =  Task::create([
            'name' => $request->name,
        ]);

    // Generate the HTML for the task
    $taskHtml = view('partials.task', ['task' => $task])->render();

    return response()->json([
        'success' => true,
        'message' => 'Task created successfully.',
        'taskHtml' => $taskHtml, // Include the HTML in the response
    ]);
    }



    public function update(Request $request, Task $task)
    {
        // Toggle the 'completed' status
        $completed = !$task->completed;

        $task->update([
            'completed' => $completed,
        ]);

        // Retrieve the updated task
        $task = $task->fresh();

        // Build a response data array
        $responseData = $task ? ['success' => true, 'message' => 'Task status updated successfully.',] : ['success' => false, 'message' => 'Task status update failed..',];

        // Return a JSON response
        return response()->json($responseData);
    }

    public function destroy(Task $task)
    {
        $task =  $task->delete();
        // Build a response data array
        $responseData = $task ? ['success' => true, 'message' => 'Task status deleted successfully.',] : ['success' => false, 'message' => 'Task status delete failed..',];

        // Return a JSON response
        return response()->json($responseData);
    }
}
