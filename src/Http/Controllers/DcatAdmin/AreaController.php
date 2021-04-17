<?php
/**
 * @author  Allen <ali@goodcatch.cn>
 */

namespace Goodcatch\Modules\Core\Http\Controllers\DcatAdmin;

use Dcat\Admin\Http\Controllers\AdminController;
use Goodcatch\Modules\Core\Repositories\DcatAdmin\AreaRepository;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;

class AreaController extends AdminController
{
    /**
     * Get content title.
     *
     * @return string
     */
    protected function title()
    {
        return $this->title ?: module_admin_trans_label();
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new ModuleRepository(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name', module_admin_trans_field('name'));
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new AreaRepository(), function (Show $show) {
            $show->row(function (Show\Row $show) {
                $show->width(6)->field('id');
                $show->width(6)->field('name', module_admin_trans_label('name'));

            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new ModuleRepository(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();

        });
    }

}
