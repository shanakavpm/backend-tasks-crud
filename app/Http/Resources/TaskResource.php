<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'user_name' => $this->user->name ?? null,
            'status' => $this->status,
            'created_at' => $this->created_at->format('d/m/Y h:i A'),
            'updated_at' => $this->updated_at->format('d/m/Y h:i A'),
        ];
    }
}
