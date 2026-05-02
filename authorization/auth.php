<?php
    session_start();
    $mode = $_GET['mode'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?v=1.1">
    <title>Document</title>
</head>
<body>
    <div class="form-container">
        <h1 id="formTitle" class="formTitle">Register</h1>
        <form action="registration.php" method="post" id="authForm">
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
            </div>
            <div class="remember-me-section" id="remember-me-section">
                <input type="checkbox" name="remember-me" id="remember-me">
                <label for="remember-me">Remember me</label>
            </div>
            <!-- remember me -->
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
    });
</script>
</html>