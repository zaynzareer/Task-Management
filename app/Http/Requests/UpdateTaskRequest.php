<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $task = $this->route('task');

        $allowedStatuses = ['pending', 'in_progress', 'completed'];

        if ($task instanceof Task) {
            $allowedStatuses = match ($task->status) {
                'pending' => ['pending', 'in_progress'],
                'in_progress' => ['in_progress', 'completed'],
                'completed' => ['completed'],
                default => ['pending', 'in_progress', 'completed'],
            };
        }

        return [
            // All fields are optional for update, but must be valid if sent.
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'due_date' => 'sometimes|nullable|date',
            'status' => ['sometimes', 'required', Rule::in($allowedStatuses)],
            'priority' => 'sometimes|nullable|integer|min:0',
        ];
    }
}
