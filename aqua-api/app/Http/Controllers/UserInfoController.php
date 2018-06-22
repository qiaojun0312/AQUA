<?php

namespace App\Http\Controllers;

use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserInfoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
   
    //新增记录
    public function add(Request $request)
    {
        $openid = $request->openid;
        $user = UserInfo::where('openid', $openid)->get();

        if(!$user->count())
        {
            //新增
            $User = new UserInfo;
            $User->from_openid = $request->from_openid;
            $User->openid = $openid;
            $User->nickname = $request->nickname;
            $User->headimgurl = $request->headimgurl;
            $User->address= '';

            $User->name = '';
            $User->phone = '';
            $User->password ='';
            $User->code = '';
            $User->isflag=0;
            
            $User->save();

            return json_encode(1);
            
        }else
        {
            return json_encode(0);
        }
    }
    //更新用户
    public function update(Request $request)
    {
        $openid = $request->openid;
        $user = UserInfo::where('openid', $openid)->get();

        if ($user->count()) {
            $user[0]->name = $request->name;
            $user[0]->phone = $request->phone;
            $user[0]->address= $request->address;

            $user[0]->save();
            return json_encode(1);
        }else
        {
            return json_encode(0);
        }
    }
}
