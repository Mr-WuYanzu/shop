<?php

namespace App\Admin\Controllers;

use App\model\Goods;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class GoodsController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Goods);

        $grid->goods_id('Goods id');
        $grid->goods_name('Goods name');
        $grid->goods_price('Goods price');
        $grid->goods_num('Goods num');
        $grid->status('Status');
        $grid->add_time('Add time');
        $grid->desc('Desc');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Goods::findOrFail($id));

        $show->goods_id('Goods id');
        $show->goods_name('Goods name');
        $show->goods_price('Goods price');
        $show->goods_num('Goods num');
        $show->status('Status');
        $show->add_time('Add time');
        $show->desc('Desc');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Goods);

        $form->number('goods_id', 'Goods id');
        $form->text('goods_name', 'Goods name');
        $form->number('goods_price', 'Goods price');
        $form->number('goods_num', 'Goods num');
        $form->text('status', 'Status');
        $form->number('add_time', 'Add time');
        $form->textarea('desc', 'Desc');

        return $form;
    }
}
