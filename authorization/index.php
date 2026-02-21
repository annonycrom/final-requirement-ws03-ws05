<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    .form-container{
        display:block;
    }
</style>
<body>
    <div class="form-container">
        <h1 id="formTitle">Register</h1>
        <form action="registration.php" method="post" id="authForm">
            <input type="text" name="first_name" id="first_name"  required>
            <input type="text" name="last_name" id="last_name" required>
            <input type="text" name="email" id="email" required>
            <input type="text" name="password" id="password" required>
            
            <input type="submit" id="submit" value="Register" >
            <p>
                <a href="#" id="verify">Already have account.</a>
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

        verify.addEventListener('click', (event)=>{
            event.preventDefault();

            if(form.action.includes('/registration.php')){
                form.action = 'login.php';
                formTitle.textContent = 'Sign In';
                submit.value = 'Sign In';
                verify.textContent = 'Register here.';

                first_name.disabled = true;
                last_name.disabled = true;
                first_name.style.display = 'none';
                last_name.style.display = 'none';
            }else{
                form.action = 'registration.php';
                formTitle.textContent = 'Sign Up';
                submit.value = 'Sign Up';
                verify.textContent = 'Already have account.';

                first_name.disabled = false;
                last_name.disabled = false;
                first_name.style.display = 'block';
                last_name.style.display = 'block';
            }
        })
    });
</script>
</html>