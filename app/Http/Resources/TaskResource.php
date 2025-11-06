<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
	    return array_merge(parent::toArray($request), [
		    'status' => $this->whenLoaded('status', function () {
			    return $this->status->name;
		    }),
	    ]);
//	    return parent::toArray($request);

    }


}
