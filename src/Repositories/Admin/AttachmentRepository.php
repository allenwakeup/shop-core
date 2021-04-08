<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Repositories\Admin;

use Goodcatch\Modules\Core\Model\Admin\Attachment;

class AttachmentRepository extends BaseRepository
{

    public static function list($perPage, $condition = [])
    {
        $data = Attachment::query()
            ->where(function ($query) use ($condition) {
                self::buildQuery($query, $condition);
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage);
        $data->transform(function ($item) {
            $item->editUrl = route('admin::attachment.edit', ['id' => $item->id]);
            $item->deleteUrl = route('admin::attachment.delete', ['id' => $item->id]);
            return $item;
        });

        return [
            'code' => 0,
            'msg' => '',
            'count' => $data->total(),
            'data' => $data->items(),
        ];
    }

    public static function add($data)
    {
        return Attachment::query()->create($data);
    }

    public static function update($id, $data)
    {
        return Attachment::query()->where('id', $id)->update($data);
    }

    public static function find($id)
    {
        return Attachment::query()->find($id);
    }

    public static function delete($id)
    {
        return Attachment::destroy($id);
    }
}
