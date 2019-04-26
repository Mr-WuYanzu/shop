<?php

namespace App\Admin\Controllers;

use App\model\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;


class UserController extends Controller
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
        $grid = new Grid(new User);

        $grid->uid('Uid');
        $grid->openid('Openid');
        $grid->user_name('用户名');
        $grid->user_sex('年龄');
        $grid->user_country('国家');
        $grid->user_province('市');
        $grid->user_city('城市');
        $grid->headimgurl('用户头像')->display(function($headimgurl){
            return "<img src='$headimgurl' style='width:50;height:50'>";
        });

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
        $show = new Show(User::findOrFail($id));

        $show->uid('Uid');
        $show->openid('Openid');
        $show->user_name('User name');
        $show->user_sex('User sex');
        $show->user_country('User country');
        $show->user_province('User province');
        $show->user_city('User city');
        $show->headimgurl('Headimgurl');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);

        $form->number('uid', 'Uid');
        $form->text('openid', 'Openid');
        $form->text('user_name', 'User name');
        $form->text('user_sex', 'User sex');
        $form->text('user_country', 'User country');
        $form->text('user_province', 'User province');
        $form->text('user_city', 'User city');
        $form->text('headimgurl', '<img src="headimgurl">');

        return $form;
    }
}
