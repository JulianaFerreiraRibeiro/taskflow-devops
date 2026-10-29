<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_task(): void
    {
        $response = $this->postJson('/tasks', [
            'title' => 'Estudar DevOps',
            'description' => 'Implementar pipeline CI',
            'status' => 'pendente',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('title', 'Estudar DevOps');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Estudar DevOps',
        ]);
    }

    public function test_can_list_tasks(): void
    {
        Task::create(['title' => 'Tarefa 1']);
        Task::create(['title' => 'Tarefa 2']);

        $response = $this->getJson('/tasks');

        $response->assertOk()
            ->assertJsonCount(2);
    }

    public function test_can_show_task(): void
    {
        $task = Task::create([
            'title' => 'Estudar Laravel',
        ]);

        $response = $this->getJson("/tasks/{$task->id}");

        $response->assertOk()
            ->assertJsonPath('title', 'Estudar Laravel');
    }

    public function test_can_update_task(): void
    {
        $task = Task::create([
            'title' => 'Tarefa inicial',
        ]);

        $response = $this->putJson("/tasks/{$task->id}", [
            'title' => 'Tarefa atualizada',
            'status' => 'em andamento',
        ]);

        $response->assertOk()
            ->assertJsonPath('title', 'Tarefa atualizada')
            ->assertJsonPath('status', 'em andamento');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Tarefa atualizada',
        ]);
    }

    public function test_can_complete_task(): void
    {
        $task = Task::create([
            'title' => 'Finalizar trabalho',
        ]);

        $response = $this->patchJson("/tasks/{$task->id}", [
            'status' => 'concluída',
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'concluída');
    }

    public function test_can_delete_task(): void
    {
        $task = Task::create([
            'title' => 'Tarefa para excluir',
        ]);

        $response = $this->deleteJson("/tasks/{$task->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_can_filter_tasks_by_status(): void
    {
        Task::create([
            'title' => 'Tarefa pendente',
            'status' => 'pendente',
        ]);

        Task::create([
            'title' => 'Tarefa concluída',
            'status' => 'concluída',
        ]);

        $response = $this->getJson('/tasks?status=pendente');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.title', 'Tarefa pendente');
    }

    public function test_cannot_create_task_without_title(): void
    {
        $response = $this->postJson('/tasks', [
            'description' => 'Tarefa sem título',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_cannot_create_task_with_invalid_status(): void
    {
        $response = $this->postJson('/tasks', [
            'title' => 'Nova tarefa',
            'status' => 'inexistente',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);

        $this->assertDatabaseCount('tasks', 0);
    }
}
