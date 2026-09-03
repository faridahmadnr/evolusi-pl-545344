<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index(): View
    {
        $tasks = Task::query()->latest()->get();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_completed' => false,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Tugas baru berhasil ditambahkan!');
    }

    /**
     * Toggle the completion status of the specified task.
     */
    public function toggle(Task $task): RedirectResponse
    {
        $task->update([
            'is_completed' => ! $task->is_completed,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Status tugas berhasil diperbarui!');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Tugas berhasil dihapus!');
    }
}
