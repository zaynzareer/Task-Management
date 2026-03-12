<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow this request only when the logged in user can create a task.
        return $this->user()?->can('create', Task::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
    * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'nullable|integer|min:0',
        ];
    }
}
