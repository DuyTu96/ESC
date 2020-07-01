<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
</head>

<body>
    OPNiDをご利用いただき、ありがとうございます。<br/><br/>
    {{$lastNameKanji}} {{$firstNameKanji}} 様の名刺コードが設定されました。<br/><br/>
    名刺コードは以下の通りです。<br/>名刺コード：{!! $body !!}<br/>URL: <a href="https://{{ config('app.subdomain_user') }}/account/business-card/">
        https://{{ config('app.subdomain_user') }}/account/business-card/
    </a><br/><br/>
    OPNiD内にある、｢アカウント > 名刺設定｣より名刺設定が行えます。<br/>
    OPNiDログイン：<a href="https://{{ config('app.subdomain_user') }}/login">
        https://{{ config('app.subdomain_user') }}/login
    </a>
    <br/><br/>
    ※本メールにお心当たりの無い方は、大変お手数ですが本メールを破棄頂きますようお願い申し上げます。<br/><br/>
    本メールアドレスは送信になりますので、本メールにご返信頂いてもお答えできませんのでご了承下さい。<br/><br/>
    ご不明な点や、お困りのことがございましたらお手数ですが以下の問い合わせフォームよりお問い合わせください。<br/><br/>
    OPNiD 問い合わせ窓口<br/>
    <a href="https://{{ config('app.subdomain_user') }}/inquiry">
        https://{{ config('app.subdomain_user') }}/inquiry
    </a>
    <br/><br/>OPNiD<br/>
    <a href="https://{{ config('app.subdomain_user') }}">
        https://{{ config('app.subdomain_user') }}
    </a>
</body>

</html>
