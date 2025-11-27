<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <!-- Add some basic styling or a CSS framework like Bootstrap -->
</head>
<body>
    <h2>School Management System - Login</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div>
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <span>{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
        </div>
        <div>
            <button type="submit">Login</button>
        </div>
    </form>
</body>
</html>