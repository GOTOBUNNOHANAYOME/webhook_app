<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form action="{{ route('login_histories.store') }}" method="POST">
        @csrf
        <input type="text" name="email">
        <input type="text" name="password">
        <button type="submit">ログイン</button>
    </form>
</body>
</html>