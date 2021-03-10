<?php

namespace Modules\Core\Repositories;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Modules\Core\Eloquent\Repository;
use Modules\Core\Entities\Channel;
use Modules\Core\Traits\FileManager;

class ChannelRepository extends Repository
{
    private $folder_path;
    private $folder = 'channel';
    use FileManager;
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return Channel::class;
    }

    public function __construct()
    {
        $this->folder_path =  storage_path('app/public/images/'). $this->folder.DIRECTORY_SEPARATOR;
    }
    /**
     * @param  array  $data
     */
    public function create(array $data)
    {
        $channel = parent::create($data);

        $channel->locales()->sync($data['locales']);

        $channel->currencies()->sync($data['currencies']);

        //upload logo image
        if (isset($data['logo'])){
            $channel->logo = $this->uploadImages($data, $channel,'logo');
            $channel->save();
        }

        //upload favicon image
        if (isset($data['favicon'])){
            $channel->logo = $this->uploadImages($data, $channel);
            $channel->save();
        }

        return $channel;
    }

    /**
     * @param  array  $data
     * @param  int  $id
     * @return Channel
     */
    public function update(array $data, $id)
    {
        $channel = $this->find($id);

        $channel = parent::update($data, $id);

        $channel->locales()->sync($data['locales']);

        $channel->currencies()->sync($data['currencies']);
        //$channel->inventory_sources()->sync($data['inventory_sources']);

        //upload logo image
        if (isset($data['logo'])){
            $channel->logo = $this->uploadImages($data, $channel,'logo');
            $channel->save();
        }

        //upload favicon image
        if (isset($data['favicon'])){
            $channel->logo = $this->uploadImages($data, $channel);
            $channel->save();
        }

        return $channel;
    }

    /**
     * @param  array  $data
     * @param  Channel  $channel
     * @param  string  $type
     * @return string
     */
    public function uploadImages($data, $channel, $type = "logo")
    {
        if (isset($data[$type]) && $data[$type] instanceof UploadedFile) {
            if (isset($channel->{$type})) {
                $this->removeFile($this->folder_path.$channel->{$type});
            }
            return $this->uploadFile($data[$type] ,$this->folder_path);

        };
    }

    public function getModel()
    {
        return app()->make($this->model());
    }

    public function index(Request $request)
    {
        $sort_by = $request->get('sort_by') ? $request->get('sort_by') : 'id';
        $sort_order = $request->get('sort_order') ? $request->get('sort_order') : 'desc';
        $channel_model = $this->getModel();
        $channels = $channel_model::query();
        if ($request->has('q')) {
            $channels->whereLike(
                $channel_model::$SEARCHABLE, $request->get('q'));
        }
        $channels = $channels->orderBy($sort_by, $sort_order);
        $limit = $request->get('limit') ? $request->get('limit') : 25;
        return $channels->paginate($limit);

    }

}
