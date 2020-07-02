<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ trans('register.title') }}</title>
</head>

<body>
    OPNiDにご登録いただき、ありがとうございます。<br/><br/>下記のURLを押して頂き、本人確認を完了させてください。
    <br/><a href="{{ $url }}">{{ $url }}</a><br/><br/><br/>
    本URLの有効期限は発行より{{ config('auth.email_auth_timeout')/60 }}分となっております。<br>
    期限が切れてしまった際には、再度アカウント登録を行なってください。<br/><br/><br/>
    ※本メールにお心当たりの無い方は、大変お手数ですが本メールを破棄頂きますようお願い申し上げます。<br/><br/>
    本メールアドレスは送信になりますので、本メールにご返信頂いてもお答えできませんのでご了承下さい。<br/><br/>
    ご不明な点や、お困りのことがございましたらお手数ですが以下の問い合わせフォームよりお問い合わせください。<br/><br/>
    OPNiD 問い合わせ窓口<br>
    <a href="https://{{ config('app.subdomain_user') }}/inquiry">
        https://{{ config('app.subdomain_user') }}/inquiry
    </a>
    <br/><br/>OPNiD<br>
    <a href="https://{{ config('app.subdomain_user') }}">
        https://{{ config('app.subdomain_user') }}
    </a>
</body>
</html>
