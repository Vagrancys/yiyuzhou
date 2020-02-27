<?php
namespace app\index\validate;
use think\Validate;
class User extends Validate
{
// 验证规则
    protected $rule = [
        'name' => 'require|min:2|max:30',
        'password' => 'require',
        'phone' =>'require|min:11|max:11|number',
        'email' => 'require|email',
        '__token__'=>'token',
        'Name' => 'require',
    ];

    protected $message= [
        'email.require'=>'邮箱必填',
        'email.email'=>'请正确输入邮箱',
        'password.require' => '密码必填',
        'name.require'=>'用户名必填',
        'Name.require'=>'用户名必填',
        'name.min'=>'用户名小于2',
        'name.max'=>'用户名大于30',
        'phone.min'=>'手机号最小11位最大11位',
        'phone.require'=>'手机号必填',
        'phone.number'=>'手机号不正确',
    ];

    protected $scene=[
        'name'=>['name','password','__token__'],
        'Name'=>['Name','password','__token__'],
        'phone'=>['phone','password','__token__'],
        'email'=>['email','password','_token___'],
    ];
}