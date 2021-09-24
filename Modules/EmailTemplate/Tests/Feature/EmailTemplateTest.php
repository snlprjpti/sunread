<?php

namespace Modules\EmailTemplate\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\EmailTemplate\Entities\EmailTemplate;

class EmailTemplateTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = EmailTemplate::class;

        parent::setUp();

        $this->admin = $this->createAdmin();
        $this->model_name = "Email Template";
        $this->route_prefix = "admin.email-templates";
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "name" => null,
            "content"=>null
        ]);
    }

    public function getNonMandodtaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "style" => null
        ]);
    }
}
