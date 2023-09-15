<tr class="task-item {{ $task->completed ? 'completed' : '' }}">
    <td>

                <label class="containers">
                    <input type="checkbox" class="task-checkbox" data-task-id="{{ $task->id }}"
                    {{ $task->completed ? 'checked' : '' }} />
                    <span class="checkmark"></span>
                  </label>
    </td>
    <td>
        {{ $task->name }}
    </td>
    <td>
        <span class="timestamp" data-timestamp="{{ $task->created_at }}"></span>
    </td>
    <td>
        <button class="btn btn-danger delete-task" data-task-id="{{ $task->id }}"><i class="fa fa-trash"></i> Delete</button>
    </td>
</tr>
