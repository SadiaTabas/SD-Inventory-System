<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Forgot Password</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            width: 400px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }

        h1 {
            text-align: center;
        }

        p {
            color: #555;
            line-height: 1.5;
        }

        input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            margin: 10px 0 15px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }

        .back {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Forgot Password</h1>

    <p>
        Enter the email address associated with your account.
        We will send you a password reset link.
    </p>

    @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">

        @csrf

        <label>Email Address</label>

        <input
            type="email"
            name="email"
            placeholder="Enter your registered email"
            required
        >

        <button type="submit">
            Send Password Reset Link
        </button>

    </form>

    <div class="back">
        <a href="{{ route('login') }}">
            Back to Login
        </a>
    </div>

</div>

</body>
</html>