<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    /**
     * Set up the user and token before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // Create a user and generate a token for them
        $this->user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);
        $this->token = $this->user->createToken('TestApp')->plainTextToken;
    }

    /**
     * Helper method to set the Authorization header.
     *
     * @return array
     */
    protected function authenticatedHeader()
    {
        return ['Authorization' => 'Bearer ' . $this->token];
    }

    public function test_index()
    {
        // Send the request to fetch tasks with the Authorization header
        $response = $this->withHeaders($this->authenticatedHeader())
                         ->getJson('/api/tasks');

        // Assert the response
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Tasks retrieved successfully.',
                 ]);
    }

    public function test_show()
    {
        // Create a task for the user
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        // Send the request to fetch the task with the Authorization header
        $response = $this->withHeaders($this->authenticatedHeader())
                         ->getJson('/api/tasks/' . $task->id);

        // Assert the response
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Task retrieved successfully.',
                     'data' => [
                         'id' => $task->id,
                         'title' => $task->title,
                         'description' => $task->description,
                         'status' => $task->status,
                         'user_name' => $task->user->name,
                     ]
                 ]);
    }

    public function test_update()
    {
        // Create a task for the user
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        // New data to update the task
        $updatedData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'completed',
        ];

        // Send the request to update the task with the Authorization header
        $response = $this->withHeaders($this->authenticatedHeader())
                         ->putJson('/api/tasks/' . $task->id, $updatedData);

        // Assert the response
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Task updated successfully.',
                     'data' => [
                         'id' => $task->id,
                         'title' => $updatedData['title'],
                         'description' => $updatedData['description'],
                         'status' => $updatedData['status'],
                         'user_name' => $task->user->name,
                     ]
                 ]);
    }

    public function test_store()
    {
        // Prepare request data
        $taskData = [
            'title' => 'New Task',
            'description' => 'This is a new task description',
            'status' => 'pending',
            'user_id' => $this->user->id,
        ];

        // Send the request to store a task
        $response = $this->withHeaders($this->authenticatedHeader())
                         ->postJson('/api/tasks', $taskData);

        // Assert the response
        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Task created successfully.',
                     'data' => [
                         'title' => 'New Task',
                         'description' => 'This is a new task description',
                         'status' => 'pending',
                         'user_name' => $this->user->name,
                     ]
                 ]);
    }

    public function test_destroy()
    {
        // Create a task for the user
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        // Send the request to delete the task with the Authorization header
        $response = $this->withHeaders($this->authenticatedHeader())
                         ->deleteJson('/api/tasks/' . $task->id);

        // Assert the response
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Task deleted successfully.',
                 ]);
    }
}
