<?php

namespace Modules\Product\Traits\ElasticSearch;

use Modules\Attribute\Entities\AttributeGroupTranslation;
use Modules\Attribute\Entities\AttributeTranslation;
use Modules\Category\Entities\CategoryTranslation;

trait CategoryFormat
{
    public function getScopeWiseCategory()
    {
        $data = [];
        
        foreach($this->categories as $category)
        {
            $data['global'][] = $category;

            foreach($this->getChannelWiseStoreID() as $store_id)
            {
                $data['store'][$store_id][] = $category->firstTranslation($store_id);
            }
        }
        return $data;
    }

//     public function getCategoryData($category)
//     {
//         return $this->categoryData = [
//             "id" => $category->id,
//             "name" => $category->name,
//             "slug" => $category->slug,
//             "position" => $category->position,
//             "image" => $category->image_url,
//             "description" => $category->description,
//             "meta_title" => $category->meta_title,
//             "meta_description" => $category->meta_description,
//             "meta_keywords" => $category->meta_keywords,
//             "_lft" => $category->_lft,
//             "_rgt" => $category->_rgt,
//             "created_at" => $category->created_at->format('M d, Y H:i A')
//         ];
//     }

//     public function getCategoryTranslationData($category, $store_id)
//     {
//         if(isset($store_id)) $storeWiseCategory = CategoryTranslation::where([
//             [ 'category_id', $category->id ],
//             [ 'store_id', $store_id ]
//         ])->first();

//         return isset($storeWiseCategory) ? array_merge($this->categoryData, [
//             "name" => $storeWiseCategory->name,
//             "description" => $storeWiseCategory->description,
//             "meta_title" => $storeWiseCategory->meta_title,
//             "meta_description" => $storeWiseCategory->meta_description,
//             "meta_keywords" => $storeWiseCategory->meta_keywords
//             ]) : $this->categoryData;
//     }
}