<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>選擇身份</title>
    <style>
        a {
            text-decoration: none;
        }
        .container {
            width: 600px;
            margin: 100px auto;
            border: solid 2px #aaa;
            box-sizing: border-box;
        }
        .header {
            margin: 0;
            padding: 10px;
            background-color: #6e8eff;
            color: #fff;
        }
        .body {
            /*padding: 10px;*/
        }
        .item {
            color: #2525e2;
            display: block;
        }
        .item div {
            height: 50px;
            line-height: 50px;
            padding: 0 15px; 
        }
        .item:hover div{
            color: #fff;
            background-color: #187672;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="header">{{ $user['name'] }} 您好，請選擇登入身份</h3>
        <div class="body">
            @foreach ($user['authInfos'] as $idx => $authInfo)
                <a href="{{ route('ntpcopenid.login.check') }}?idx={{ $idx }}" class="item">
                    <div>
                        {{ $authInfo['name'] }}&nbsp;&nbsp;&nbsp;&nbsp;
                        {{ $authInfo['role'] }}&nbsp;&nbsp;&nbsp;&nbsp;
                        {{ implode($authInfo['groups'], ', ') }}
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</body>
</html>