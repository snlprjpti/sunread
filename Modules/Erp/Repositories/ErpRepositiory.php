<?php

namespace Modules\Erp\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\Erp\Traits\HasErpMapper;

class ErpRepositiory extends BaseRepository
{
	use HasErpMapper;

	public function __construct()
	{

	}

	public function list(object $request)
	{
		dd($this->getList());
	}
}
