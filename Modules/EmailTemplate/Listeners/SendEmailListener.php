<?php

namespace Modules\EmailTemplate\Listeners;

use Modules\EmailTemplate\Jobs\SendEmailJob;

class SendEmailListener
{
   public function sendEmail(string $event, int $entity_id)
   {
       SendEmailJob::dispatch( $event, $entity_id );
   }
}
