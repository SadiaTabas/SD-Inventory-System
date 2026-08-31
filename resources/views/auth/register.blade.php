<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registration</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 400px;
            margin: 60px auto;
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

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #555;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .success {
            color: green;
            margin-bottom: 15px;
        }

        .login-link {
            text-align: center;
            margin: 15px 0 20px;
        }

        .login-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Registration</h1>

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

    <form method="POST" action="{{ route('register') }}">

        @csrf

        {{-- Name --}}
        <div class="form-group">
            <label>Name</label>
            <input
                type="text"
                name="name"
                placeholder="Enter your full name"
                value="{{ old('name') }}"
                required
            >
        </div>

        {{-- Email --}}
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

        {{-- Phone Number --}}
        <div class="form-group">
            <label>Phone Number</label>
            <input
                type="text"
                name="phone"
                placeholder="e.g. 01700000000"
                value="{{ old('phone') }}"
            >
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label>Password</label>
            <input
                type="password"
                name="password"
                placeholder="Min 8 chars, 1 uppercase, 1 number, 1 symbol"
                required
            >
        </div>

        {{-- Confirm Password --}}
        <div class="form-group">
            <label>Confirm Password</label>
            <input
                type="password"
                name="password_confirmation"
                placeholder="Confirm your password"
                required
            >
        </div>

        <p class="login-link">
            Already have an account?
            <a href="{{ route('login') }}">Login here</a>
        </p>

        <button type="submit">
            Sign Up
        </button>

    </form>

</div>

</body>
</html>