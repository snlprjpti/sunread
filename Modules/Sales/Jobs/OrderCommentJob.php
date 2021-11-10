<?php

namespace Modules\Sales\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Sales\Entities\OrderComment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderCommentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $comment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(object $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try
        {
            OrderComment::create([
                "order_id" => $this->comment->order_id,
                "user_id" => auth("customer")->id(),
                "is_customer_notified" => $this->comment->is_customer_notified ?? 0,
                "is_visible_on_storefornt" => $this->comment->is_visible_on_storefornt ?? 0,
                "comment" => $this->comment->comment,
                "created_at" => now()
            ]);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}
