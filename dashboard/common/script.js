// toas

// usermanagement section
    document.addEventListener('DOMContentLoaded', () => {
    // 1. Select both types of buttons
    // Note: Use '.btn-archive' to match your HTML class
    const actionButtons = document.querySelectorAll('.reset-btn, .btn-archive');

    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            
            // 2. Handle the Archive confirmation specifically
            
            
            e.preventDefault(); // Stop page from jumping/reloading
            
            const url = this.getAttribute('href');
            const toast = document.getElementById('toast');

            // 3. Perform the background update
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    // Show the message in your toast
                    toast.textContent = data.message;
                    toast.className = `toast show ${data.status}`;

                    // 4. If it was an Archive, fade out and remove the row
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

                    // 5. Auto-hide toast after 3 seconds
                    setTimeout(() => { 
                        toast.className = 'toast'; 
                    }, 3000);
                })
                .catch(err => {
                    console.error("Error:", err);
                    // Optional: Show error toast if the network fails
                });
        });
    });
});