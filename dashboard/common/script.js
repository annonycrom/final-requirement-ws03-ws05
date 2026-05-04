    // taost notification function
    function showToast(message, type){  
        const toast = document.getElementById("toast");
        if(!toast) return;
        toast.innerText = message;
        toast.className = `toast show ${type}`;
        setTimeout (() => toast.classList.remove("show"), 3000);
        if(type === "success"){
            setTimeout(() => {location.reload();}, 2500);
        }
    }

    
    // usermanagement section
    document.addEventListener('DOMContentLoaded', () => {
    const actionButtons = document.querySelectorAll('.reset-btn, .btn-archive');

    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            
            
            
            e.preventDefault();
            
            const url = this.getAttribute('href');
            const toast = document.getElementById('toast');
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    // Show the message in your toast
                    toast.textContent = data.message;
                    toast.className = `toast show ${data.status}`;

                    if (data.status === 'success' && this.classList.contains('btn-archive')) {
                        const row = this.closest('tr');
                        const tableBody = row.parentNode;
                        row.style.transition = '0.5s';
                        row.style.opacity = '0';
                        setTimeout(() => {row.remove();
                        
                        if (tableBody.querySelectorAll('tr').length === 0) {
                            location.reload();
                        }

                    }, 500);
                    
                        
                    }

                    setTimeout(() => { 
                        toast.className = 'toast'; 
                    }, 3000);
                })
                .catch(err => {
                    console.error("Error:", err);
                });
        });
    });

    // adding new user/admin

    const userForm = document.getElementById('addNew');

    if(userForm){
        userForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(userForm);
            const targetForm = userForm.getAttribute('action');
            try{
                const response = await fetch(targetForm,{
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if(result.status === 'success'){
                    const toas = document.getElementById('toast');
                    showToast(result.message, 'success');
                    userForm.reset();
                }else{
                    showToast(result.message, 'error');
                }
            }catch(error){
                    showToast("System Error: Could not connect.", 'danger');
            }
        });
    }
});