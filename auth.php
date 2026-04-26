<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco-Catalog | Join the Movement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-card { width: 100%; max-width: 400px; border: none; border-radius: 15px; }
        .btn-success { border-radius: 50px; padding: 10px 20px; font-weight: 600; }
        .toggle-link { cursor: pointer; color: #198754; text-decoration: underline; }
    </style>
</head>
<body>

<div class="card shadow-lg auth-card">
    <div class="card-body p-5">
        <div class="text-center mb-4">
            <h2 id="auth-title" class="fw-bold text-success">Login</h2>
            <p id="auth-subtitle" class="text-muted">Welcome back, Eco-Warrior!</p>
        </div>

        <form id="authForm">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" id="email" class="form-control" placeholder="name@example.com" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-success w-100 mb-3" id="submitBtn">Sign In</button>
        </form>

        <div class="text-center">
            <small id="toggle-text">
                Don't have an account? <span class="toggle-link" onclick="toggleAuth()">Register here</span>
            </small>
        </div>
    </div>
</div>

<script>
    let isLogin = true;

    function toggleAuth() {
        isLogin = !isLogin;
        document.getElementById('auth-title').innerText = isLogin ? 'Login' : 'Register';
        document.getElementById('auth-subtitle').innerText = isLogin ? 'Welcome back, Eco-Warrior!' : 'Start tracking your impact.';
        document.getElementById('submitBtn').innerText = isLogin ? 'Sign In' : 'Create Account';
        document.getElementById('toggle-text').innerHTML = isLogin 
            ? 'Don\'t have an account? <span class="toggle-link" onclick="toggleAuth()">Register here</span>'
            : 'Already have an account? <span class="toggle-link" onclick="toggleAuth()">Login here</span>';
    }

    document.getElementById('authForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const endpoint = isLogin ? 'login.php' : 'register.php';

        const formData = new FormData();
        formData.append('email', email);
        formData.append('password', password);

        try {
            const response = await fetch(endpoint, { method: 'POST', body: formData });
            const result = await response.json();

            if (response.ok) {
                alert(result.message || "Success!");
                if (isLogin) {
                    window.location.href = 'index.php'; // Redirect to dashboard
                } else {
                    toggleAuth(); // Switch to login mode after registering
                }
            } else {
                alert(result.error || "Something went wrong.");
            }
        } catch (err) {
            alert("Connection error.");
        }
    });
</script>

</body>
</html>