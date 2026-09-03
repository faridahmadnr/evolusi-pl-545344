<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_tasks_page(): void
    {
        Task::create([
            'title' => 'Belajar GitHub Actions',
            'description' => 'Membuat CI pipeline dengan 2 jobs',
            'is_completed' => false,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Belajar GitHub Actions');
    }

    public function test_user_can_create_a_task(): void
    {
        $response = $this->post('/tasks', [
            'title' => 'Tugas Baru Evolusi PL',
            'description' => 'Mengerjakan branch protection rule',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Tugas Baru Evolusi PL',
            'description' => 'Mengerjakan branch protection rule',
            'is_completed' => false,
        ]);
    }

    public function test_task_creation_requires_a_title(): void
    {
        $response = $this->post('/tasks', [
            'title' => '',
            'description' => 'Deskripsi tanpa judul',
        ]);

        $response->assertSessionHasErrors('title');
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_user_can_toggle_task_completion_status(): void
    {
        $task = Task::create([
            'title' => 'Task yang akan diselesaikan',
            'is_completed' => false,
        ]);

        $response = $this->patch(route('tasks.toggle', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertTrue($task->fresh()->is_completed);

        // Toggle back
        $response = $this->patch(route('tasks.toggle', $task));
        $this->assertFalse($task->fresh()->is_completed);
    }

    public function test_user_can_delete_a_task(): void
    {
        $task = Task::create([
            'title' => 'Task yang akan dihapus',
            'is_completed' => false,
        ]);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}
