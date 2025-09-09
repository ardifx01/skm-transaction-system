<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: sans-serif;
        }

        h1 {
            color: #333;
        }

        .content {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <h1>{{ $title }}</h1>
    <p class="content">Tanggal: {{ $date }}</p>
</body>

</html>
