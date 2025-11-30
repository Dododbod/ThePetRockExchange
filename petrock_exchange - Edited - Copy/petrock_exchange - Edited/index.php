<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Rock Exchange</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🪨 Pet Rock Exchange 🪨</h1>

        <!-- Login Form -->
        <form id="authForm">
            <input type="text" id="email" placeholder="Email" required>
            <input type="password" id="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <p>Don't have an account? <a href="#" id="toggleRegister">Register here</a></p>
        </form>

        <!-- Register Form -->
        <form id="registerForm" style="display:none;">
            <input type="text" id="fname" placeholder="First Name" required>
            <input type="text" id="lname" placeholder="Last Name" required>
            <input type="date" id="dob" required>
            <input type="text" id="address" placeholder="Address" required>
            <input type="email" id="regEmail" placeholder="Email" required>
            <input type="password" id="regPassword" placeholder="Password" required>
            <button type="submit">Create Account</button>
            <p>Already have an account? <a href="#" id="toggleLogin">Login here</a></p>
        </form>

        <p id="message"></p>
    </div>

    <!-- Scattered Rocks Script -->
    <script>
        const rockCount = 400; 
        for (let i = 0; i < rockCount; i++) {
            const rock = document.createElement('div');
            rock.textContent = '🪨';
            rock.style.position = 'absolute';
            rock.style.left = Math.random() * 100 + 'vw';
            rock.style.top = Math.random() * 100 + 'vh';
            rock.style.fontSize = 20 + Math.random() * 40 + 'px'; // random size
            rock.style.opacity = 0.05 + Math.random() * 0.15; // subtle opacity
            rock.style.transform = `rotate(${Math.random() * 360}deg)`; // random rotation
            rock.style.pointerEvents = 'none'; // don't block clicks
            rock.style.zIndex = '0'; // behind container
            document.body.appendChild(rock);
        }
    </script>

    <script src="script.js"></script>
</body>
</html>
