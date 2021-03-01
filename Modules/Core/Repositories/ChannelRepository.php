<?php

namespace Modules\Core\Repositories;


use Illuminate\Support\Facades\Storage;
use Modules\Core\Eloquent\Repository;
use Modules\Core\Entities\Channel;


class ChannelRepository extends Repository
{

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return Channel::class;
    }

    /**
     * @param  array  $data
     */
    public function create(array $data)
    {
        $model = $this->getModel();

        $channel = parent::create($data);

        $channel->locales()->sync($data['locales']);

        $channel->currencies()->sync($data['currencies']);

        $this->uploadImages($data, $channel);

        $this->uploadImages($data, $channel, 'favicon');

        return $channel;
    }

    /**
     * @param  array  $data
     * @param  int  $id
     * @param  string  $attribute
     * @return Channel
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $channel = $this->find($id);

        $channel = parent::update($data, $id, $attribute);

        $channel->locales()->sync($data['locales']);

        $channel->currencies()->sync($data['currencies']);

        //$channel->inventory_sources()->sync($data['inventory_sources']);

        $this->uploadImages($data, $channel);

        $this->uploadImages($data, $channel, 'favicon');

        return $channel;
    }

    /**
     * @param  array  $data
     * @param  Channel  $channel
     * @param  string  $type
     * @return void
     */
    public function uploadImages($data, $channel, $type = "logo")
    {
        if (isset($data[$type])) {
            foreach ($data[$type] as $imageId => $image) {
                $file = $type . '.' . $imageId;
                $dir = 'channel/' . $channel->id;

                if (request()->hasFile($file)) {
                    if ($channel->{$type}) {
                        Storage::delete($channel->{$type});
                    }

                    $channel->{$type} = request()->file($file)->store($dir);
                    $channel->save();
                }
            }
        } else {
            if ($channel->{$type}) {
                Storage::delete($channel->{$type});
            }

            $channel->{$type} = null;
            $channel->save();
        }
    }

    public function getModel()
    {
        return app()->make($this->model());
    }
}
