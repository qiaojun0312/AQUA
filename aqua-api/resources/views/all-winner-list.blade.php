<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All User List</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

</head>
<body>
<table class="table table-hover">
    <tr>
        <th>ID</th>
        <th>OpenID</th>
        <th>昵称</th>
        <th>头像</th>
        <th>姓名</th>
        <th>电话</th>
        <th>分数</th>
        <th>等级</th>
        <th>中奖类型</th>
        <th>中奖时间</th>
    </tr>
    @foreach ($userwinner as $userinfo)
    <tr>
        <td>{{ $userinfo->id }}</td>
        <td>{{ $userinfo->openid }}</td>
        <td>{{ $userinfo->nickname }}</td>
        <td><img width="32" height="32" src="{{ $userinfo->headimgurl }}"/></td>
        <td>{{ $userinfo->name }}</td>
        <td>{{ $userinfo->phone }}</td>
        <td>{{ $userinfo->score }}</td>
        <td>{{ $userinfo->stage }}</td>
        <td>{{ $userinfo->zjtype }}</td>
        <td>{{ $userinfo->created_at }}</td>
    </tr>
    @endforeach

</table>
</body>
</html>