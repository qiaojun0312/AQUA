<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Log;

class WeChatController extends Controller
{

    public function oauth(Request $request)
    {
        session(['target_url' => $request->redirecturl]);
        //Log::info("=====base64_encode to target_url:".$request->redirecturl);

        $wechat_user = session()->get('wechat.oauth_user');
        $data = base64_encode(json_encode($wechat_user));
        //Log::info("=====".json_encode($wechat_user->getOriginal()));

        $target_url = base64_decode(session()->get('target_url'));
        $target_url = $target_url.(strrpos($target_url, "?") > -1 ? "&data=" : "?data=").$data;
        //Log::info("======base64_decode to target_url with base64_encode wechat_user: ".$target_url);

        return response()->redirectTo($target_url);
    }

    public function jssdk(Request $request)
    {
        app('wechat')->js->setUrl($request->pageurl);
        $jssdk = app('wechat')->js->config(array('onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone', 'openLocation', 'getLocation'), false);
        return $jssdk;
    }

}


