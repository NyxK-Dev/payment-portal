<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body { font-family: Arial, Helvetica, sans-serif; color: #333; }
    .container { max-width: 600px; margin: 24px auto; padding: 16px; border: 1px solid #eaeaea; border-radius: 8px; }
    .code { font-size: 22px; font-weight: bold; background: #f7f7f7; padding: 10px 14px; display: inline-block; letter-spacing: 4px; }
    .btn { display: inline-block; padding: 10px 14px; background: #0d6efd; color: #fff; text-decoration: none; border-radius: 6px; }
  </style>
</head>
<body>
  <div class="container">
    <h2><?= html_escape(getenv('APP_NAME') ?: 'Payment Portal'); ?> — Email verification</h2>
    <p>Hello <?= html_escape($user->name ?: $user->email); ?>,</p>
    <p>Use the code below to verify your email address. It expires in <?= getenv('VERIF_TTL_MINUTES') ?: 60; ?> minutes.</p>
    <p class="code"><?= html_escape($code); ?></p>
    <p>If you didn't request this, just ignore this email.</p>
    <p>Thanks,<br>The <?= html_escape(getenv('APP_NAME') ?: 'Payment Portal'); ?> team</p>
  </div>
</body>
</html>
