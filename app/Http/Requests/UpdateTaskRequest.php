<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get the task from the route '/tasks/{task}'.
        $task = $this->route('task');

        // Only allow update when the user owns this task.
        return $task instanceof Task
            && ($this->user()?->can('update', $task) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
    * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // All fields are optional for update, but must be valid if sent.
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'due_date' => 'sometimes|nullable|date',
            'status' => 'sometimes|required|in:pending,in_progress,completed',
            'priority' => 'sometimes|nullable|integer|min:0',
        ];
    }
}
