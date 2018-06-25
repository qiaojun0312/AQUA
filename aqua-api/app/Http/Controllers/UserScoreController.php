<?php

namespace App\Http\Controllers;

use App\Models\UserScore;
use Illuminate\Http\Request;
use App\Models\UserInfo;
use Illuminate\Support\Facades\DB;

class UserScoreController extends Controller
{
    public function __construct()
    {
        //
    }
    //获得所有分数排行榜
    public function getallscore()
    {
        $users_score = DB::select("select uscore.id,fromuser.from_openid,fromuser.openid,fromuser.name,fromuser.nickname,fromuser.phone,fromuser.headimgurl,fromuser.address,
                uscore.score,uscore.stage,uscore.created_at
                from user_scores as uscore
                left join user_infos as fromuser on uscore.openid =fromuser.openid 
                order by uscore.score desc");

        return json_encode($users_score);
    }
    //获取当天分数排行榜
    public function getscorebydate()
    {
        $created_at= date("Y-m-d");

        $users_score = DB::select("select uscore.id,fromuser.from_openid,fromuser.openid,fromuser.name,fromuser.nickname,fromuser.phone,fromuser.headimgurl,fromuser.address,uscore.score,uscore.stage,uscore.created_at
                    from user_scores as uscore 
                    left join user_infos as fromuser on uscore.openid =fromuser.openid 
                    where uscore.created_at like '".$created_at."%' order by uscore.score desc ");

        return json_encode($users_score);
    }
    //获取某一个用户当天分数
    public function getscorebyopenid($openid)
    {
        $created_at= date("Y-m-d");

        $users_score = DB::select("select uscore.id,fromuser.from_openid,fromuser.openid,fromuser.name,fromuser.nickname,fromuser.phone,fromuser.headimgurl,fromuser.address,uscore.score,uscore.stage,uscore.created_at
                from user_scores as uscore 
                left join user_infos as fromuser on uscore.openid =fromuser.openid 
                where uscore.openid ='".$openid."' and uscore.created_at like '".$created_at."%'");

        return json_encode($users_score);
    }
    //获取某一个用户的好友排行榜
    public function getscorebyfromopenid($fromopenid)
    {
        $users_score = DB::select("
      select scoreuser.* from ( 
                select uscore.id,fromuser.from_openid,fromuser.openid,fromuser.name,fromuser.nickname,fromuser.phone,fromuser.headimgurl,fromuser.address,uscore.score,uscore.stage,uscore.created_at
                from user_scores as uscore 
                left join user_infos as fromuser on uscore.openid =fromuser.openid 
                where fromuser.from_openid ='".$fromopenid."' order by uscore.score desc  ) as scoreuser limit 3");

        return json_encode($users_score);
    }

    //新增记录
    public function add(Request $request)
    {
        $openid = $request->openid;
        $created_at= date("Y-m-d");

        $user = UserScore::where('openid', $openid)->where('created_at', 'like', $created_at.'%')->get();
        //return json_encode($user);

        if ($user!=null && $user->count()) {

            $curscore=$request->score;
            $oldscroe=$user[0]->score;

            $curstage=$request->stage;
            $oldstage=$user[0]->stage;

            //如果当前分数大于数据库分数就修改，否则不修改
            if($curscore > $oldscroe && $curstage >$oldstage)
            {
                $user[0]->score = $curscore;
                $user[0]->stage = $curstage;

                $user[0]->save();
                return json_encode(2); //修改成功
            }else
            {
                return json_encode(3); //未修改，当前分数小于数据库分数
            }

        }else
        {
            //新增
            $User = new UserScore;
            $User->openid = $openid;
            $User->score = $request->score;
            $User->stage = $request->stage;

            $User->save();

            return json_encode(1);//新增成功
        }

    }

    /**************************后台查看数据************************************/
    public function getallscoreforview()
    {
        $users_score = DB::select(
            "select scoreuser.* from ( 
                    select uscore.id,fromuser.from_openid,fromuser.openid,fromuser.name,fromuser.nickname,fromuser.phone,fromuser.headimgurl,fromuser.address,
                uscore.score,uscore.stage,uscore.created_at
                from user_scores as uscore
                left join user_infos as fromuser on uscore.openid =fromuser.openid 
                order by uscore.score desc ) as scoreuser limit 1000");

        return view('all-list', ["userscores" => $users_score]);
    }
    public function destory($userscoreid)
    {
        DB::table('user_scores')->where('id', '=', $userscoreid)->delete();
        return 1;
    }
    public function destoryuser($openid)
    {
        DB::beginTransaction();
        try
        {
            DB::table('user_winners')->where('openid', '=', $openid)->delete();
            DB::table('user_scores')->where('openid', '=', $openid)->delete();
            DB::table('user_infos')->where('openid', '=', $openid)->delete();
            DB::commit();
            return 1;
        }catch (Exception $ex)
        {
            DB::rollback();
            throw $ex;
        }
    }

}
