<?php

namespace App\Console;


use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\UserWinner;
use App\Models\UserInfo;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
       // \App\Console\Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {


            //当前日期
            $created_at= date("Y-m-d");
            $users_winner = DB::select("select *
                            from user_winners as uswinner
                            where uswinner.created_at like '".$created_at."%'");
            $totalcount =count($users_winner);

            //如果数据库有当天记录就说明生成过了，不执行生成中奖操作
            if($totalcount <= 0)
            {
                DB::beginTransaction();
                try {

                    //昨天分数排行榜
                    $users_score = DB::select("select uscore.id,fromuser.openid
                                    from user_scores as uscore
                                    left join user_infos as fromuser on uscore.openid =fromuser.openid
                                    where uscore.created_at like '" . $created_at . "%' and fromuser.isflag=0  order by uscore.score desc ");

                    $arrayTop5 = array();//前5名
                    $arrayOther = array();//除去前5剩下的
                    $i = 0;
                    foreach ($users_score as $scores) {
                        if ($i < 5) //获取前5名
                        {
                            array_push($arrayTop5, $scores->openid);
                        } else //获取除了前5名之外的
                        {
                            array_push($arrayOther, $scores->openid);
                        }
                        $i++;
                    }

                    //新增排名前5的中奖用户
                    foreach ($arrayTop5 as $arrayTop5item) {
                        //新增
                        $UserWinner = new UserWinner();
                        $UserWinner->openid = $arrayTop5item;
                        $UserWinner->zjtype = 'top5';

                        $UserWinner->save();

                        //修改用户是否中奖标示
                        $user = UserInfo::where('openid', $arrayTop5item)->get();
                        if ($user->count()) {
                            $user[0]->isflag = 1;
                            $user[0]->save();
                        }
                    }
                    if ($i > 4) {
                        //从剩下的数组中随机产生5个中奖用户
                        $arrayAfter5 = array_rand($arrayOther, 5);

                        //新增随机产生的5名用户
                        foreach ($arrayAfter5 as $arrayAfter5item) {
                            //新增
                            $UserWinner = new UserWinner();
                            $UserWinner->openid = $arrayOther[$arrayAfter5item];
                            $UserWinner->zjtype = 'random';

                            $UserWinner->save();

                            //修改用户是否中奖标示
                            $user = UserInfo::where('openid', $UserWinner->openid)->get();
                            if ($user->count()) {
                                $user[0]->isflag = 1;
                                $user[0]->save();
                            }
                        }
                    }

                    DB::commit();
                    Log::info("执行成功！");
                } catch (Exception $ex) {
                    DB::rollback();
                    throw $ex;
                    Log::info("执行失败！");
                }
            }else
            {
                Log::info("当天已经执行过了！");
            }

        })->dailyAt('23:55');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
