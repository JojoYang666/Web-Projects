<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class CreateForm extends Form
{
    public function buildForm()
    {
        $this
            ->add('title', 'text',['label' => '表单标题','rules' => 'required'])
            ->add('description', 'textarea',['label' => '表单描述','rules' => 'max:500'])
            ->add('publish', 'checkbox',['label' => '是否发布'])
            ->add('wechat', 'checkbox',['label' => '是否用于微信'])
            ->add('filterBlacklist', 'checkbox',['label' => '是否过滤黑名单'])
            ->add('reviewTimes','number',['label' => '审核次数','rules' => 'required|max:10'])
            ->add('fields','hidden',['attr' => ['id'=>'fields']])
            ->add('fieldNames','hidden',['attr' => ['id'=>'fieldNames']])
            ->add('submit', 'submit',['attr' => ['id'=>'submit']])
            ->add('clear', 'reset', ['label' => '重置']);
    }
}
