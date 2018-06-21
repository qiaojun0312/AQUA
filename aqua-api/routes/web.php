<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function(){
    return 'API Version: 2.0';
});

Route::get('jssdk', 'WeChatController@jssdk');

Route::group(['middleware' => ['web', 'wechat.oauth']], function () {
    Route::get('oauth', 'WeChatController@oauth');
});

Route::post('user/add', 'UserInfoController@add');//新增用户
Route::post('user/update', 'UserInfoController@update');//修改用户

Route::post('userscore/add', 'UserScoreController@add');//新增用户分数
Route::get('userscore/getall', 'UserScoreController@getallscore');//获得所有排行榜
Route::get('userscore/getscorebydate', 'UserScoreController@getscorebydate');//获取当前分数排行榜
Route::get('userscore/getuserstage/{openid}', 'UserScoreController@getscorebyopenid');//获取某一个用户当天分数
Route::get('userscore/getfriendstage/{fromopenid}', 'UserScoreController@getscorebyfromopenid');//获取某一个用户的好友排行榜


Route::get('userscore/view-all', 'UserScoreController@getallscoreforview');//获得所有排行榜视图


Route::get('userwinner/top10', 'UserWinnerController@getscoretop10');//获每天中奖名单
Route::get('userwinner/view-all', 'UserWinnerController@getall');//获得所有中奖名单
Route::get('userwinner/generate', 'UserWinnerController@generatetop10');//生成中奖名单