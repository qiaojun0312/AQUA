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
    //获得单条记录
    public function getUserInfo($openid)
    {
        $user = UserInfo::where('openid', $openid)->get();
        return json_encode($user);
    }
    //获得所有记录
    public function getList()
    {
        $users = DB::table('user_infos')->groupby("champion","openid")->orderby("created_at")->get();

        return view('all-list', ["userinfos" => $users]);
    }
    public function getListforapi()
    {
        $users = DB::table('user_infos')->groupby("champion","openid")->orderby("created_at")->get();
        return json_encode($users);
    }
    //获得所有分享记录
    public function getShareList()
    {
        $users = DB::select(
    "select fromuser.openid as share_openid,
            fromuser.nickname as share_nickname,
            fromuser.headimgurl as share_headimgurl,
            fromuser.champion as share_champion,
            fromuser.group_name as share_group_name,
            users.*
            from user_infos as users
            left join user_infos as fromuser on users.from_openid =fromuser.openid 
            where users.from_openid <> ''
            group by users.updated_at desc");

        return view('all-share-list', ["userinfos" => $users]);
    }
    //根据fromopenid获得记录，根据分享人的openid获得列表
    public function getListByFromOpenid($openid)
    {
        $users = DB::table('user_infos')->where("from_openid",$openid)->get();
        return json_encode($users);
    }
    //根据fromopenid获得记录，根据分享人的openid获得列表
    public function getListBychampion($champion)
    {
        $users = DB::table('user_infos')->where("champion",$champion)->get();
        return json_encode($users);
    }
    //根据投票国家统计数量列表
    public function getcountytplist()
    {
        $users = DB::select("select champion ,count(*) as total from user_infos group by champion");
        return json_encode($users);
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

            $user[0]->save();
            return json_encode(1);
        }else
        {
            return json_encode(0);
        }
    }
}
