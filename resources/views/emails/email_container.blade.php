<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Repairer - Email Container</title>
    {{ $stylesheet }}
    <link href="{{ asset('css/email-container.css') }}" rel="stylesheet">
    
</head>

<body bgcolor="#f7f9fa">
<table class="body-wrap" bgcolor="#f7f9fa">
    <tr>
        <td></td>
        <td class="container" bgcolor="#FFFFFF">
            <div class="content">
                <table>
                    <tr>
                        <td><h2>{{ $logo }}</h2></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="clear:both;height:15px;"></div>
                            <strong>{{ $heading }}</strong>
                            <div style="clear:both;height:15px;"></div>
                            {{ $msg }}
                            <div style="clear:both;height:25px;"></div>
                            <strong>{{ $site_name }}</strong>
                            <p>{{ $site_link }}</p>
                            <div style="clear:both;height:15px;"></div>
                            <p style="border-top:1px solid #CCC;margin-bottom:0;">This email is sent to {{ $name }} ({{ $email }}).</p>
                        </td>
                    </tr>
                </table>
            </div>

        </td>
        <td></td>
    </tr>
</table>
{{ $email_footer }}
</body>
</html>