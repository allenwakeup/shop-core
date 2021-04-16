<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\LightCMS\Admin;

use Goodcatch\Modules\Core\Handlers\AttachmentUploadHandler;
use Goodcatch\Modules\Lightcms\Http\Controllers\Controller;
use Goodcatch\Modules\Core\Http\Requests\Admin\AttachmentRequest;
use Goodcatch\Modules\Core\Repositories\Admin\AttachmentRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Goodcatch\Modules\Core\Model\Admin\Attachment;

class AttachmentController extends Controller
{
    protected $formNames = [];

    public function __construct()
    {
        parent::__construct();

        $this->breadcrumb[] = ['title' => '附件列表', 'url' => route('admin::' . module_route_prefix ('.') . 'core.attachment.index')];
    }

    /**
     * 附件管理-附件列表
     *
     */
    public function index()
    {
        $this->breadcrumb[] = ['title' => '附件列表', 'url' => ''];
        return view('core::admin.attachment.index', ['breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 附件列表数据接口
     *
     * @param Request $request
     * @return array
     */
    public function list(Request $request)
    {
        $perPage = (int) $request->get('limit', 50);
        $condition = $request->only($this->formNames);

        $data = AttachmentRepository::list($perPage, $condition);

        return $data;
    }

    /**
     * 附件管理-新增附件
     *
     */
    public function create()
    {
        $this->breadcrumb[] = ['title' => '新增附件', 'url' => ''];
        return view('core::admin.attachment.add', ['breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 附件管理-保存附件
     *
     * @param AttachmentRequest $request
     * @param AttachmentUploadHandler $uploader
     * @return array
     */
    public function save(AttachmentRequest $request, AttachmentUploadHandler $uploader)
    {

        if (array_has(Attachment::ATTACHABLES_MAPPING, $request->attachable))
        {
            $attachable_class = Attachment::ATTACHABLES_MAPPING [$request->attachable];
            $attachable = (new $attachable_class ())->find($request->id);
            $user = \Auth::user();
            // 附件必须与用户相关，必须有对应的对象，且支持以下两个判断
            if (isset ($attachable) && ($attachable->isBelongToUser($user) || $attachable->isBelongToProxyUser($user)))
            {
                try {
                    $attachable
                        ->attachments()
                        ->create($uploader
                            ->save($request->file, $request->attachable, 'app')
                        );
                    return [
                        'code' => 0,
                        'msg' => '上传成功',
                        'redirect' => true
                    ];
                } catch (QueryException $e) {
                    return [
                        'code' => 1,
                        'msg' => '上传失败：' . (Str::contains($e->getMessage(), 'Duplicate entry') ? '当前附件已存在' : '其它错误'),
                        'redirect' => false
                    ];
                }

            } else {
                return [
                    'code' => 1,
                    'msg' => '上传失败：未找到目标对象',
                    'redirect' => false
                ];
            }
        } else {
            return [
                'code' => 1,
                'msg' => '上传失败：未允许的上传类型',
                'redirect' => false
            ];
        }

    }

    /**
     * 附件管理-编辑附件
     *
     * @param int $id
     * @return View
     */
    public function edit($id)
    {
        $this->breadcrumb[] = ['title' => '编辑附件', 'url' => ''];

        $model = AttachmentRepository::find($id);
        return view('core::admin.attachment.add', ['id' => $id, 'model' => $model, 'breadcrumb' => $this->breadcrumb]);
    }

    /**
     * 附件管理-更新附件
     *
     * @param AttachmentRequest $request
     * @param int $id
     * @return array
     */
    public function update(AttachmentRequest $request, $id)
    {
        $data = $request->only($this->formNames);
        try {
            AttachmentRepository::update($id, $data);
            return [
                'code' => 0,
                'msg' => '编辑成功',
                'redirect' => true
            ];
        } catch (QueryException $e) {
            return [
                'code' => 1,
                'msg' => '编辑失败：' . (Str::contains($e->getMessage(), 'Duplicate entry') ? '当前附件已存在' : '其它错误'),
                'redirect' => false
            ];
        }
    }

    /**
     * 附件管理-删除附件
     *
     * @param AttachmentRequest $request
     * @param int $id
     * @return array
     */
    public function delete(AttachmentRequest $request, $id)
    {
        if (array_has(Attachment::ATTACHABLES_MAPPING, $request->attachable))
        {
            $attachable_class = Attachment::ATTACHABLES_MAPPING [$request->attachable];
            $attachable = (new $attachable_class ())->find($request->attachable_id);
            $user = \Auth::user();
            // 附件必须与用户相关，必须有对应的对象，且支持以下两个判断
            if (isset ($attachable) && ($attachable->isBelongToUser($user) || $attachable->isBelongToProxyUser($user)))
            {
                try {
                    AttachmentRepository::delete($id);
                    return [
                        'code' => 0,
                        'msg' => '删除成功',
                        'redirect' => route('admin::' . module_route_prefix ('.') . 'core.attachment.index')
                    ];
                } catch (\RuntimeException $e) {
                    return [
                        'code' => 1,
                        'msg' => '删除失败：' . $e->getMessage(),
                        'redirect' => false
                    ];
                }

            } else {
                return [
                    'code' => 1,
                    'msg' => '删除失败：未找到目标对象',
                    'redirect' => false
                ];
            }
        } else {
            return [
                'code' => 1,
                'msg' => '删除失败：未允许的上传类型',
                'redirect' => false
            ];
        }

    }

    /**
     * 附件管理-下载附件
     *
     * @param AttachmentRequest $request
     * @param int $id
     * @return array
     */
    public function download(AttachmentRequest $request, $attachable_id, $attachable_type, $id)
    {
        if (array_has(Attachment::ATTACHABLES_MAPPING, $attachable_type))
        {
            $attachable_class = Attachment::ATTACHABLES_MAPPING [$attachable_type];
            $attachable = (new $attachable_class ())->with('attachments')->find($attachable_id);
            $user = \Auth::user();
            // 附件必须与用户相关，必须有对应的对象，且支持以下两个判断
            if (isset ($attachable) && ($attachable->isBelongToUser($user) || $attachable->isBelongToProxyUser($user)))
            {
                $attachment = $attachable->attachments->filter(function ($item, $key) use ($id) {
                    return ($item->id . '') === ($id . '');
                })->first();
                if (! is_null ($attachment))
                {
                    $file = storage_path() . $attachment->path;
                    if (file_exists($file) && is_readable($file)) {

                        return response()->download($file);
                    } else {
                        $message = '附件文件不存在或不可读取';
                    }
                } else {
                    $message = '附件不存在';
                }
            } else {
                $message = '没有权限下载';
            }
        } else {
            $message = '没有找到匹配类型';
        }
        $this->breadcrumb[] = ['title' => '下载附件', 'url' => ''];
        return view('core::admin.attachment.download', ['id' => $id, 'message' => $message, 'breadcrumb' => $this->breadcrumb]);
    }

}
