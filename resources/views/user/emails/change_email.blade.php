<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ trans('auth.change_email_subject') }}</title>
</head>

<body>
    OPNiDをご利用いただき、ありがとうございます。<br/><br/>下記URLを押して頂き、メールアドレス変更処理を完了させてください。
    <br/><a href="{{ $url }}">{{ $url }}</a><br/><br/><br/>本URLの有効期限は発行より{{ config('auth.email_auth_timeout')/60 }}分となっております。<br>
    期限が切れてしまった際には、再度ログイン画面よりメールアドレスの変更を行なってください。<br/><br/><br/>
    ※本メールにお心当たりのない方は、誤ってどなたかがアドレスを入力した可能性がございますのでこのまま削除いただきますようお願いいたします。
    <br/><br/>本メールアドレスは送信になりますので、本メールにご返信頂いてもお答えできませんのでご了承下さい。<br/><br/>
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
