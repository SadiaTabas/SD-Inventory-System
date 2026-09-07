<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
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
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .remember-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
        }

        .remember-group input[type="checkbox"] {
            width: auto;
            margin: 0;
            cursor: pointer;
        }

        .remember-group label {
            display: inline;
            margin-bottom: 0;
            cursor: pointer;
            font-size: 14px;
            color: #555;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #555;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .links {
            text-align: center;
            margin-top: 20px;
        }

        .links a {
            color: #333;
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Login</h1>

    @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">

        @csrf

        <div class="form-group">
            <label>Email Address</label>

            <input
                type="email"
                name="email"
                placeholder="Enter your email"
                value="{{ old('email') }}"
                required
            >
        </div>

        <div class="form-group">
            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Enter your password"
                required
            >
        </div>

        <div class="remember-group">
            <input
                type="checkbox"
                name="remember"
                id="remember"
                value="1"
                {{ old('remember') ? 'checked' : '' }}
            >
            <label for="remember">Remember Me</label>
        </div>

        <button type="submit">
            Login
        </button>

    </form>

    <div class="links">

        <p>
            <a href="{{ route('password.request') }}">
                Forgot your password?
            </a>
        </p>

        <p>
            Don't have an account?
            <a href="{{ route('register') }}">
                Create an account
            </a>
        </p>

    </div>

</div>

</body>
</html>