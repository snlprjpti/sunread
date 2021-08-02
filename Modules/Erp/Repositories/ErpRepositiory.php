<?php

namespace Modules\Erp\Repositories;

use Illuminate\Support\Facades\Storage;
use Modules\Core\Repositories\BaseRepository;
use Modules\Erp\Jobs\EanCodes;
use Modules\Erp\Jobs\ErpAttributeGroups;
use Modules\Erp\Jobs\ErpProductDescription;
use Modules\Erp\Jobs\ListProducts;
use Modules\Erp\Jobs\Mapper\ErpMigrateProductJob;
use Modules\Erp\Jobs\MigrateProductJob;
use Modules\Erp\Jobs\ProductImages;
use Modules\Erp\Jobs\ProductVariants;
use Modules\Erp\Jobs\SalePrices;
use Modules\Erp\Jobs\WebAssortments;
use Modules\Erp\Jobs\WebInventories;
use Modules\Erp\Traits\HasErpMapper;
use Modules\Erp\Traits\HasErpValueMapper;

class ErpRepositiory extends BaseRepository
{
	use HasErpMapper, HasErpValueMapper;


	public function list(object $request)
	{
        // dd("done");  

		// WebAssortments::dispatch();
		// dd("asd");
        dd($this->importAll());
		ErpMigrateProductJob::dispatch();
		dd("done");
		ProductImages::dispatch();
		EanCodes::dispatch();
		ErpAttributeGroups::dispatch();
		// ErpProductDescription::dispatch();
		ListProducts::dispatch();
		ProductVariants::dispatch();
		SalePrices::dispatch();
		
		WebInventories::dispatch();
		dd("done");
	}
}
