<?php

namespace App\Http\Controllers;

use App\Models\UserWinner;
use App\Models\UserInfo;
use Illuminate\Support\Facades\DB;

class UserWinnerController extends Controller
{
    public function __construct()
    {
        //
    }

    //获取当天中奖名单
    public function getscoretop10()
    {
        //昨天日期
        //$created_at= date("Y-m-d",strtotime("-1 day"));
        //$created_at= date("Y-m-d");
        $users_winner = DB::select("select uswinner.id,fromuser.openid,fromuser.name,fromuser.nickname,fromuser.phone,fromuser.headimgurl,fromuser.address,
                uscore.score,uscore.stage,uswinner.zjtype,DATE_FORMAT(uswinner.created_at, \"%Y-%m-%d\") as created_at
                    from  user_winners as uswinner 
                    left join user_scores as uscore on  uswinner.openid=uscore.openid
                    left join user_infos as fromuser on uswinner.openid =fromuser.openid
                    GROUP BY uswinner.openid,DATE_FORMAT(uswinner.created_at, \"%Y-%m-%d\")
                    order by uscore.score,DATE_FORMAT(uswinner.created_at, \"%Y-%m-%d\") desc");

//        $arrayDate= array();
//        foreach ($users_winner as $winneritem)
//        {
//            if(!array_indx($arrayDate,$winneritem->created_at))
//            {
//                array_push($arrayDate,$winneritem->created_at);
//            }
//        }

        return json_encode($users_winner);
    }


    //获取所有中奖名单
    public function getall()
    {
        $users_score = DB::select("select uswinner.id,fromuser.openid,fromuser.name,fromuser.nickname,fromuser.phone,fromuser.headimgurl,fromuser.address,
                uscore.score,uscore.stage,uswinner.created_at,uswinner.zjtype
                    from  user_winners as uswinner 
                    left join user_scores as uscore on  uswinner.openid=uscore.openid
                    left join user_infos as fromuser on uswinner.openid =fromuser.openid
                    group by fromuser.openid order by uswinner.id ");

        return view('all-winner-list', ["userwinner" => $users_score]);
    }


    public function destory($userwinnerid)
    {
        DB::table('user_winners')->where('id', '=', $userwinnerid)->delete();
        return 1;
    }

    //生成中奖名单
    public function generatetop10()
    {
        DB::beginTransaction();
        try
        {
                //当前日期
                $created_at= date("Y-m-d");

                //昨天分数排行榜
                $users_score = DB::select("select uscore.id,fromuser.openid
                            from user_scores as uscore
                            left join user_infos as fromuser on uscore.openid =fromuser.openid
                            where uscore.created_at like '".$created_at."%' and fromuser.isflag=0  order by uscore.score desc ");
                $totalcount =count($users_score);

                $arrayTop5= array();//前5名
                $arrayOther= array();//除去前5剩下的
                $i=0;
                foreach ($users_score as $scores)
                {
                    if($i<5) //获取前5名
                    {
                        array_push($arrayTop5,$scores->openid);
                    }else //获取除了前5名之外的
                    {
                        array_push($arrayOther,$scores->openid);
                    }
                    $i++;
                }

                //新增排名前5的中奖用户
                foreach ($arrayTop5 as $arrayTop5item)
                {
                    //新增
                    $UserWinner = new UserWinner();
                    $UserWinner->openid = $arrayTop5item;
                    $UserWinner->zjtype = 'top5';

                    $UserWinner->save();

                    //修改用户是否中奖标示
                    $user = UserInfo::where('openid', $arrayTop5item)->get();
                    if($user->count()) {
                        $user[0]->isflag = 1;
                        $user[0]->save();
                    }
                }
                if($i>4)
                {
                    //从剩下的数组中随机产生5个中奖用户
                    $arrayAfter5=array_rand($arrayOther,5);

                    //新增随机产生的5名用户
                    foreach ($arrayAfter5 as $arrayAfter5item)
                    {
                        //新增
                        $UserWinner = new UserWinner();
                        $UserWinner->openid = $arrayOther[$arrayAfter5item];
                        $UserWinner->zjtype = 'random';

                        $UserWinner->save();

                        //修改用户是否中奖标示
                        $user = UserInfo::where('openid', $UserWinner->openid)->get();
                        if($user->count()) {
                            $user[0]->isflag = 1;
                            $user[0]->save();
                        }
                    }
                }

            DB::commit();
            echo "执行成功！";
        }catch (Exception $ex)
        {
            DB::rollback();
            throw $ex;
            echo "执行失败！";
        }
    }

}
