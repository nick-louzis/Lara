<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h3><a href="/" target="_blank" rel="noopener noreferrer">Home</a></h3>
    <h3>Date: {{  date('D/M/Y')  }}</h3>
    <span>{{$name}}</span>

    <ul>
        @foreach ($animals as $animal)
            <li>{{$animal}}</li>
        @endforeach
    </ul>
</body>
</html>