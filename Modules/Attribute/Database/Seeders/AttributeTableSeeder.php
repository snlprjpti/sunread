<?php

namespace Modules\Attribute\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Attribute\Entities\Attribute;

class AttributeTableSeeder extends Seeder
{

    public function run()
    {
        Model::reguard();
        DB::table('attributes')->delete();
        DB::table('attribute_translations')->delete();

        $now = Carbon::now()->toDateTimeString();

        $attributes_array = [
            ['id' => '1','slug' => 'sku','name' => 'SKU','type' => 'text','validation' => NULL,'position' => '1','is_required' => '1','is_unique' => '1','value_per_locale' => '0','value_per_channel' => '0','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '0','is_visible_on_front' => '0','use_in_flat' => '1','created_at' => $now,'updated_at' => $now],

            ['id' => '2','slug' => 'name','name' => 'Name','type' => 'text','validation' => NULL,'position' => '2','is_required' => '1','is_unique' => '0','value_per_locale' => '1','value_per_channel' => '1','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '0','is_visible_on_front' => '0',
            'use_in_flat' => '1','created_at' => $now,'updated_at' => $now],



            ['id' => '3','slug' => 'new','name' => 'New','type' => 'boolean','validation' => NULL,'position' => '5','is_required' => '0','is_unique' => '0','value_per_locale' => '0','value_per_channel' => '0','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '0','is_visible_on_front' => '0',
            'use_in_flat' => '1','created_at' => $now,'updated_at' => $now],

            ['id' => '4','slug' => 'featured','name' => 'Featured','type' => 'boolean','validation' => NULL,'position' => '6','is_required' => '0','is_unique' => '0','value_per_locale' => '0','value_per_channel' => '0','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '0','is_visible_on_front' => '0',
            'use_in_flat' => '1','created_at' => $now,'updated_at' => $now],

            ['id' => '5','slug' => 'visible_individually','name' => 'Visible Individually','type' => 'boolean','validation' => NULL,'position' => '7','is_required' => '1','is_unique' => '0','value_per_locale' => '0','value_per_channel' => '0','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '0','is_visible_on_front' => '0','created_at' => $now,
            'use_in_flat' => '1','updated_at' => $now],

            ['id' => '6','slug' => 'status','name' => 'Status','type' => 'boolean','validation' => NULL,'position' => '8','is_required' => '1','is_unique' => '0','value_per_locale' => '0','value_per_channel' => '0','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '0','is_visible_on_front' => '0',
            'use_in_flat' => '1','created_at' => $now,'updated_at' => $now],

            ['id' => '7','slug' => 'short_description','name' => 'Short Description','type' => 'textarea','validation' => NULL,'position' => '9','is_required' => '1','is_unique' => '0','value_per_locale' => '1','value_per_channel' => '1','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '0',
            'is_visible_on_front' => '0','use_in_flat' => '1','created_at' => $now,'updated_at' => $now],

            ['id' => '8','slug' => 'description','name' => 'Description','type' => 'textarea','validation' => NULL,'position' => '10','is_required' => '1','is_unique' => '0','value_per_locale' => '1','value_per_channel' => '1','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '0','is_visible_on_front' => '0',
            'use_in_flat' => '1','created_at' => $now,'updated_at' => $now],

            ['id' => '9','slug' => 'price','name' => 'Price','type' => 'price','validation' => 'decimal','position' => '11','is_required' => '1','is_unique' => '0','value_per_locale' => '0','value_per_channel' => '0','is_filterable' => '1','is_configurable' => '0','is_user_defined' => '0','is_visible_on_front' => '0',
            'use_in_flat' => '1','created_at' => $now,'updated_at' => $now],

            ['id' => '10','slug' => 'cost','name' => 'Cost','type' => 'price','validation' => 'decimal','position' => '12','is_required' => '0','is_unique' => '0','value_per_locale' => '0','value_per_channel' => '1','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '1','is_visible_on_front' => '0',
            'use_in_flat' => '1','created_at' => $now,'updated_at' => $now],

            ['id' => '11','slug' => 'special_price','name' => 'Special Price','type' => 'price','validation' => 'decimal','position' => '13','is_required' => '0','is_unique' => '0','value_per_locale' => '0','value_per_channel' => '0','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '0','is_visible_on_front' => '0','use_in_flat' => '1','created_at' => $now,'updated_at' => $now],

            ['id' => '12','slug' => 'special_price_from','name' => 'Special Price From','type' => 'date','validation' => NULL,'position' => '14','is_required' => '0','is_unique' => '0','value_per_locale' => '0','value_per_channel' => '1','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '0','is_visible_on_front' => '0','use_in_flat' => '1','created_at' => $now,'updated_at' => $now],

            ['id' => '13','slug' => 'special_price_to','name' => 'Special Price To','type' => 'date','validation' => NULL,'position' => '15','is_required' => '0','is_unique' => '0','value_per_locale' => '0','value_per_channel' => '1','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '0',
                'use_in_flat' => '1','is_visible_on_front' => '0','created_at' => $now,'updated_at' => $now],

            ['id' => '14','slug' => 'meta_title','name' => 'Meta Title','type' => 'textarea','validation' => NULL,'position' => '16','is_required' => '0','is_unique' => '0','value_per_locale' => '1','value_per_channel' => '1','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '0','is_visible_on_front' => '0',
            'use_in_flat' => '1','created_at' => $now,'updated_at' => $now],

            ['id' => '15','slug' => 'meta_keywords','name' => 'Meta Keywords','type' => 'textarea','validation' => NULL,'position' => '17','is_required' => '0','is_unique' => '0','value_per_locale' => '1','value_per_channel' => '1','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '0','is_visible_on_front' => '0',
                'use_in_flat' => '1','created_at' => $now,'updated_at' => $now],

            ['id' => '16','slug' => 'meta_description','name' => 'Meta Description','type' => 'textarea','validation' => NULL,'position' => '18','is_required' => '0','is_unique' => '0','value_per_locale' => '1','value_per_channel' => '1','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '1','is_visible_on_front' => '0','use_in_flat' => '1','created_at' => $now,'updated_at' => $now],

            ['id' => '17','slug' => 'width','name' => 'Width','type' => 'text','validation' => 'decimal','position' => '19','is_required' => '0','is_unique' => '0','value_per_locale' => '0','value_per_channel' => '0','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '1','is_visible_on_front' => '0',
            'use_in_flat' => '1','created_at' => $now,'updated_at' => $now],

            ['id' => '18','slug' => 'height','name' => 'Height','type' => 'text','validation' => 'decimal','position' => '20','is_required' => '0','is_unique' => '0','value_per_locale' => '0','value_per_channel' => '0','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '1','is_visible_on_front' => '0',
            'use_in_flat' => '1','created_at' => $now,'updated_at' => $now],

            ['id' => '19','slug' => 'depth','name' => 'Depth','type' => 'text','validation' => 'decimal','position' => '21','is_required' => '0','is_unique' => '0','value_per_locale' => '0','value_per_channel' => '0','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '1','is_visible_on_front' => '0',
            'use_in_flat' => '1','created_at' => $now,'updated_at' => $now],
            ['id' => '20','slug' => 'weight','name' => 'Weight','type' => 'text','validation' => 'decimal','position' => '22','is_required' => '1','is_unique' => '0','value_per_locale' => '0','value_per_channel' => '0','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '0','is_visible_on_front' => '0',
            'use_in_flat' => '1','created_at' => $now,'updated_at' => $now],
            ['id' => '21','slug' => 'color','name' => 'Color','type' => 'select','validation' => NULL,'position' => '23','is_required' => '0','is_unique' => '0','value_per_locale' => '0','value_per_channel' => '0','is_filterable' => '1','is_configurable' => '1','is_user_defined' => '1','is_visible_on_front' => '0',
            'use_in_flat' => '1','created_at' => $now,'updated_at' => $now],
            ['id' => '22','slug' => 'size','name' => 'Size','type' => 'select','validation' => NULL,'position' => '24','is_required' => '0','is_unique' => '0','value_per_locale' => '0','value_per_channel' => '0','is_filterable' => '1','is_configurable' => '1','is_user_defined' => '1','is_visible_on_front' => '0',
             'use_in_flat' => '1','created_at' => $now,'updated_at' => $now],
            ['id' => '23','slug' => 'brand','name' => 'Brand','type' => 'select','validation' => NULL,'position' => '25','is_required' => '0','is_unique' => '0','value_per_locale' => '0','value_per_channel' => '0','is_filterable' => '1','is_configurable' => '0','is_user_defined' => '0','is_visible_on_front' => '1',
             'use_in_flat' => '1','created_at' => $now,'updated_at' => $now],
            ['id' => '24','slug' => 'guest_checkout','name' => 'Guest Checkout','type' => 'boolean','validation' => NULL,'position' => '8','is_required' => '1','is_unique' => '0','value_per_locale' => '0','value_per_channel' => '0','is_filterable' => '0','is_configurable' => '0','is_user_defined' => '0','is_visible_on_front' => '0',
             'use_in_flat' => '1','created_at' => $now,'updated_at' => $now],
        ];


        foreach ($attributes_array as $array){
            //initialising via constructor to allow only fillable columns
            $attribute = Attribute::firstOrNew(['id' => $array['id']]);
            $attribute->fill($array);
            $attribute->id = $array['id'];
            $attribute->save();

        }


        DB::table('attribute_translations')->insert([
           ['id' => '1','locale' => 'en','name' => 'SKU','attribute_id' => '1'],
           ['id' => '2','locale' => 'en','name' => 'Name','attribute_id' => '2'],
           ['id' => '3','locale' => 'en','name' => 'New','attribute_id' => '3'],
           ['id' => '4','locale' => 'en','name' => 'Featured','attribute_id' => '4'],
           ['id' => '5','locale' => 'en','name' => 'Visible Individually','attribute_id' => '5'],
           ['id' => '6','locale' => 'en','name' => 'Status','attribute_id' => '6'],
           ['id' => '7','locale' => 'en','name' => 'Short Description','attribute_id' => '7'],
           ['id' => '8','locale' => 'en','name' => 'Description','attribute_id' => '8'],
           ['id' => '9','locale' => 'en','name' => 'Price','attribute_id' => '9'],
           ['id' => '10','locale' => 'en','name' => 'Cost','attribute_id' => '10'],
           ['id' => '11','locale' => 'en','name' => 'Special Price','attribute_id' => '11'],
           ['id' => '12','locale' => 'en','name' => 'Special Price From','attribute_id' => '12'],
           ['id' => '13','locale' => 'en','name' => 'Special Price To','attribute_id' => '13'],
           ['id' => '14','locale' => 'en','name' => 'Meta title','attribute_id' => '14'],
           ['id' => '15','locale' => 'en','name' => 'Meta Keywords','attribute_id' => '15'],
           ['id' => '16','locale' => 'en','name' => 'Meta Description','attribute_id' => '16'],
           ['id' => '17','locale' => 'en','name' => 'Width','attribute_id' => '17'],
           ['id' => '18','locale' => 'en','name' => 'Height','attribute_id' => '18'],
           ['id' => '19','locale' => 'en','name' => 'Depth','attribute_id' => '19'],
           ['id' => '20','locale' => 'en','name' => 'Weight','attribute_id' => '20'],
           ['id' => '21','locale' => 'en','name' => 'Color','attribute_id' => '21'],
           ['id' => '22','locale' => 'en','name' => 'Size','attribute_id' => '22'],
           ['id' => '23','locale' => 'en','name' => 'Brand','attribute_id' => '23'],

        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $attribute_groups_mapping =   [

            ['attribute_id' => '1','attribute_group_id' => '1','position' => '1'],
            ['attribute_id' => '2','attribute_group_id' => '1','position' => '2'],
            ['attribute_id' => '3','attribute_group_id' => '1','position' => '3'],
            ['attribute_id' => '4','attribute_group_id' => '1','position' => '4'],
            ['attribute_id' => '5','attribute_group_id' => '1','position' => '5'],
            ['attribute_id' => '6','attribute_group_id' => '1','position' => '6'],


            ['attribute_id' => '7','attribute_group_id' => '2','position' => '1'],
            ['attribute_id' => '8','attribute_group_id' => '2','position' => '2'],

            ['attribute_id' => '9','attribute_group_id' => '4','position' => '1'],
            ['attribute_id' => '10','attribute_group_id' => '4','position' => '2'],
            ['attribute_id' => '11','attribute_group_id' => '4','position' => '3'],
            ['attribute_id' => '12','attribute_group_id' => '4','position' => '4'],
            ['attribute_id' => '13','attribute_group_id' => '4','position' => '5'],

            ['attribute_id' => '14','attribute_group_id' => '3','position' => '1'],
            ['attribute_id' => '15','attribute_group_id' => '3','position' => '2'],
            ['attribute_id' => '16','attribute_group_id' => '3','position' => '3'],

            ['attribute_id' => '17','attribute_group_id' => '5','position' => '1'],
            ['attribute_id' => '18','attribute_group_id' => '5','position' => '2'],
            ['attribute_id' => '19','attribute_group_id' => '5','position' => '3'],
            ['attribute_id' => '20','attribute_group_id' => '5','position' => '4'],

            ['attribute_id' => '20','attribute_group_id' => '1','position' => '7'],
            ['attribute_id' => '21','attribute_group_id' => '1','position' => '8'],
            ['attribute_id' => '22','attribute_group_id' => '1','position' => '9'],

        ];

        foreach ($attribute_groups_mapping as $map){
            $attribute = Attribute::find($map['attribute_id']);
            if(isset($attribute))
                $attribute->update($map);

        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}