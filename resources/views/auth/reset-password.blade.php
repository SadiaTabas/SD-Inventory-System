<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reset Password</title>

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

        small {
            color: #555;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Reset Password</h1>

    @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">

        @csrf

        <input
            type="hidden"
            name="token"
            value="{{ $token }}"
        >

        <div class="form-group">

            <label>Email Address</label>

            <input
                type="email"
                name="email"
                value="{{ $email }}"
                required
            >

        </div>

        <div class="form-group">

            <label>New Password</label>

            <input
                type="password"
                name="password"
                placeholder="Enter new password"
                required
            >

            <small>
                At least 8 characters, including uppercase,
                lowercase, number and special character.
            </small>

        </div>

        <div class="form-group">

            <label>Confirm New Password</label>

            <input
                type="password"
                name="password_confirmation"
                placeholder="Confirm new password"
                required
            >

        </div>

        <button type="submit">
            Reset Password
        </button>

    </form>

</div>

</body>
</html>