<?php

namespace Modules\Erp\Repositories;

use Illuminate\Support\Facades\Storage;
use Modules\Core\Repositories\BaseRepository;
use Modules\Erp\Jobs\ErpAttributeGroups;
use Modules\Erp\Jobs\ListProducts;
use Modules\Erp\Jobs\ProductImages;
use Modules\Erp\Traits\HasErpMapper;

class ErpRepositiory extends BaseRepository
{
	use HasErpMapper;

	public function __construct()
	{

	}

	public function list(object $request)
	{
		// ProductImages::dispatch();
		dd($this->storeDescription());
		// dd($this->storeImage());
		// dd(storage_path('app\public'));
		// ->get("/1511111_965_a.jpg")
		// $storage = Storage::disk("ftp")->path("/1511111_965_a.jpg");


		// ListProducts::dispatchSync();
		// ErpAttributeGroups::dispatchSync();
		// dd($this->allData());
	}
}
