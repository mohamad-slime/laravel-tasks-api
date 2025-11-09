<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
	protected $model = Task::class;

	public function definition()
	{
		// Decide if task is completed (~30% chance)
		$isCompleted = $this->faker->boolean(30);

		// Ensure there is at least one status and user when factory runs:
		$userId = User::inRandomOrder()->value('id') ?? User::factory()->create()->id;

		return [
			'user_id'      => $userId,
			'title'        => $this->faker->sentence(mt_rand(3, 8)),
			'description'  => $this->faker->optional(0.8)->paragraphs(mt_rand(1,3), true),
			'status'    => $this->faker->randomElement(['pending', 'in_progress', 'completed']),
			'priority'     => $this->faker->randomElement(['low','medium','high']),
			'due_date'     => $this->faker->optional(0.7)->dateTimeBetween('now', '+90 days'),
			'completed_at' => $isCompleted ? $this->faker->dateTimeBetween('-30 days', 'now') : null,
			'created_at'   => $this->faker->dateTimeBetween('-120 days', 'now'),
			'updated_at'   => now(),
		];
	}

	// optional: named state for completed tasks
	public function completed()
	{
		return $this->state(function (array $attributes) {
			return [
				'completed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
			];
		});
	}
}
