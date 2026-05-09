<?php
    session_start();
    $mode = $_GET['mode'] ?? '';

    if(empty($_SESSION['csrf_token'])){
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?v=1.1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
    <div class="form-container">
        <h1 id="formTitle" class="formTitle">Register</h1>
        <form action="registration.php" method="post" id="authForm">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'];?>">
            <div class="input_container" id="firstname_input">
                <input type="text" name="first_name" id="first_name"  required>
                <label for="first_name">Firstname</label>
                <span class="underline"></span>
            </div>
            <div class="input_container" id="lastname_input">
                <input type="text" name="last_name" id="last_name" required>
                <label for="first_name">Lastname</label>
                <span class="underline"></span>
            </div>
            <div class="input_container">
                <input type="text" name="email" id="email" required>
                <label for="first_name">Email</label>
                <span class="underline"></span>
            </div>
            <div class="input_container">
                <input type="password" name="password" id="password" required>
                <label for="first_name">Password</label>
                <span class="underline"></span>
                <i class="fas fa-eye" id="togglePassword"></i>
            </div>
            <!-- remember me -->
            <div class="remember-me-section" id="remember-me-section">
                <input type="checkbox" name="remember-me" id="remember-me">
                <label for="remember-me">Remember me</label>
            </div>
            
            <p class="invalid <?php echo (isset($_GET['error'])) ? 'show' : ''; ?>" id="invalid">
                <?php 
                    if (isset($_GET['error']) && $_GET['error'] == 'Archived') {
                        echo "Account is inactive.";
                    } else {
                        echo "Invalid Credentials.";
                    }
                ?>
            </p>

            <input type="submit" id="submit" value="Register" >
            <p>
                <a href="javascript:void(0)" id="verify">Already have account.</a>
            </p>
        </form>
    </div>
    <div id="toast" class="toast"></div>
</body>
<script>
    document.addEventListener('DOMContentLoaded',()=>{
        const first_name = document.getElementById('first_name');
        const last_name = document.getElementById('last_name');
        const form = document.getElementById('authForm');
        const verify = document.getElementById('verify');
        const submit = document.getElementById('submit');
        const formTitle = document.getElementById('formTitle');
        const firstname_input = document.getElementById('firstname_input');
        const lastname_input = document.getElementById('lastname_input');
        const rememberMe = document.getElementById('remember-me-section');
        verify.addEventListener('click', (event)=>{
            event.preventDefault();

            if(form.action.includes('registration.php')){
                form.action = 'login-process.php';
                formTitle.textContent = 'Sign In';  
                submit.value = 'Sign In';
                verify.textContent = 'Register here.';

                first_name.disabled = true;
                last_name.disabled = true;
                firstname_input.classList.add('hidden');
                lastname_input.classList.add('hidden');
                rememberMe.classList.remove('hidden');
            }else{
                form.action = 'registration.php';
                formTitle.textContent = 'Register';
                submit.value = 'Register';
                verify.textContent = 'Already have account.';

                first_name.disabled = false;
                last_name.disabled = false;
                firstname_input.classList.remove("hidden");
                lastname_input.classList.remove("hidden");
                rememberMe.classList.add('hidden');
            }
        });
        const urlParam = new URLSearchParams(window.location.search);
        if (urlParam.get('mode') === 'login'){
            verify.click();
        }

        const errormessage = document.getElementById('invalid');
        const inputs = form.querySelectorAll('input');

        inputs.forEach(input => {
            input.addEventListener('input', ()=>{
                errormessage.classList.remove('show');
            });
            input.addEventListener('click',()=>{
                errormessage.classList.remove('show');
            })

        });
    // taost function
         function showToast(message, type){  
            const toast = document.getElementById("toast");
            if(!toast) return;
            toast.innerText = message;
            toast.className = `toast show ${type}`;
            setTimeout (() => toast.classList.remove("show"), 3000);
            if(type === "success"){
                // Redirect to login or home after success
                setTimeout(() => { window.location.href = "auth.php?mode=login"; }, 2500);
            }
        }

        form.addEventListener('submit', function(e) {
            // Only use AJAX for registration
            if (form.action.includes('registration.php')) {
                e.preventDefault();

                const password = document.getElementById('password').value.trim();
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

                // console.log("Password:", password);
                // console.log("Length:", password.length);
                // console.log("Passes Regex:", passwordRegex.test(password));

                // if (!passwordRegex.test(password)) {
                //     showToast("Password must have at least 8 characters...", "error");
                //     return;
                // }
                if (!passwordRegex.test(password)) {
                    showToast("Password must have at least 8 characters, including 1 uppercase, 1 lowercase, and 1 number.", "error");
                    return; // Stop the form from submitting
                }
                const formData = new FormData(this);

                fetch('registration.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showToast(data.message, "success");
                        // The reload is handled inside your showToast function
                    } else {
                        showToast(data.message, "error");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast("An error occurred.", "error");
                });
            }
        });

        


        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');

        togglePassword.addEventListener('click', function () {
            // Toggle the type attribute
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle the icon classes
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });


    });
</script>
</html>