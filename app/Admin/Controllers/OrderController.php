<?php

namespace App\Admin\Controllers;

use App\model\Order;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class OrderController extends Controller
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
        $grid = new Grid(new Order);

        $grid->oid('Oid');
        $grid->uid('Uid');
        $grid->order_sn('Order sn');
        $grid->order_amount('Order amount');
        $grid->add_time('Add time');
        $grid->pay_time('Pay time');
        $grid->pay_amount('Pay amount');
        $grid->is_del('Is del');
        $grid->pay_status('Pay status');

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
        $show = new Show(Order::findOrFail($id));

        $show->oid('Oid');
        $show->uid('Uid');
        $show->order_sn('Order sn');
        $show->order_amount('Order amount');
        $show->add_time('Add time');
        $show->pay_time('Pay time');
        $show->pay_amount('Pay amount');
        $show->is_del('Is del');
        $show->pay_status('Pay status');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order);

        $form->number('oid', 'Oid');
        $form->number('uid', 'Uid');
        $form->text('order_sn', 'Order sn');
        $form->decimal('order_amount', 'Order amount');
        $form->number('add_time', 'Add time');
        $form->number('pay_time', 'Pay time');
        $form->decimal('pay_amount', 'Pay amount');
        $form->text('is_del', 'Is del');
        $form->text('pay_status', 'Pay status');

        return $form;
    }
}
