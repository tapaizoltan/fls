<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,bold">
    <title>fls :: financial lead service [<?php echo 'v.'.ENV("APP_VERSION");?>]</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Montserrat';
            background-color: white;
        }
        .mainContainer {
            margin: 0 auto 0 auto;
            max-width: 100%;
            height: auto;
        }
        .flsHeader {
            width: 100%;
            height:100px;
        }
        .flsLogoLine {
            width: 100%;
            height: 64px;
            background-image: url('images/fls_index_bg.png');
            background-color: #e2e3e4;
            background-position: center top;
            background-repeat: no-repeat;
            /*background-attachment: fixed;*/
            background-size:inherit;
        }
        .flsCommandLine {
            
        }
        .flsCommandBox {
            margin: 0 auto 0 auto;
            width:380px;
            height: 380px;
            padding:10px;
        }
        .boxAttentionText {
            color:#da7a3b;
            font-size: 60pt;
            font-weight: 100;
        }
        .boxOpportunities {
            padding-top:20px;
            color:#1f1d1d;
            font-size: 12pt;
            font-weight:300pika;
            text-align: justify;
        }
        .boxStart {
            padding-top: 20px;
            color:#1f1d1d;
            font-size: 12pt;
            font-weight:300pika;
            text-align: justify;
        }
        .buttonStart {
            background-color: #d87b3a;
            border-radius: 10px;
            border: none;
            color: white;
            padding: 10px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="mainContainer">
        <div class="flsHeader"></div>
        <div class="flsLogoLine"></div>
        <div class="flsCommandLine">
            <div class="flsCommandBox">
                <div class="boxAttentionText"></div>
                <div class="boxOpportunities">A rendszer használatához be kell jelentkezni!</div>
                <div class="boxStart">
                    <center><a href="/admin" class="buttonStart">Bejelentkezés</a></center>
                </div>
            </div>
        </div>
        <div class="flsFooter"></div>
    </div>
</body>
</html>