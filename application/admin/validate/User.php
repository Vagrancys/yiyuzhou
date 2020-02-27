<?php
namespace app\admin\validate;
use think\Validate;
class User extends Validate
{
// 验证规则
    protected $rule = [
        'admin_name' => 'require',
        'admin_password' => 'require',
        'code'=>'require|captcha'
    ];

    protected $message= [
        'admin_name.require'=>'用户名必填',
        'admin_password.require' => '密码必填',
        'code.require' => '验证码必填',
    ];
}