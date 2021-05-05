<?php

namespace Modules\Review\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Customer\Transformers\CustomerResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "customer" => new CustomerResource($this->customer),
            "rating" => $this->rating,
            "title" => $this->title,
            "description" => $this->description,
            "review_votes" => ReviewVoteResource::collection($this->review_votes),
            "review_replies" => ReviewReplyResource::collection($this->review_replies),
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
