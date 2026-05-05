  // reload prevent
    window.addEventListener('DOMContentLoaded', () => {
        const pageKey = window.location.pathname + '-tab';
        let saved = localStorage.getItem(pageKey);
        
        // 1. FALLBACK: If nothing is saved, find the button that is 'active' in the HTML
        if (!saved) {
            const defaultBtn = document.querySelector('.nav-btn.active');
            if (defaultBtn) {
                // This extracts 'new-admin-container' from your onclick string
                const match = defaultBtn.getAttribute('onclick').match(/'([^']+)'/);
                saved = match ? match[1] : null;
            }
        }

        const targetSection = document.getElementById(saved);
        const targetBtn = document.querySelector(`[onclick*="${saved}"]`);

        // 2. Clear everything first to be sure
        document.querySelectorAll('.tab-content').forEach(sec => sec.classList.add('hidden'));
        document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.remove('active'));

        // 3. Show the target (saved or default)
        if (targetSection) {
            targetSection.classList.remove('hidden');
            if (targetBtn) targetBtn.classList.add('active');
        } else {
            // 4. LAST RESORT: If even the default fails, show the very first section found
            const firstSection = document.querySelector('.tab-content');
            if (firstSection) {
                firstSection.classList.remove('hidden');
                // Find the matching button for the first section
                document.querySelector(`[onclick*="${firstSection.id}"]`)?.classList.add('active');
            }
        }
    });



    // tab switching in the dashboard
    function showSection(event, sectionID) {
    // Generate a unique key based on the current page URL (e.g., "super-admin-dashboard-tab")
    const pageKey = window.location.pathname + '-tab';
    localStorage.setItem(pageKey, sectionID);

    // Your existing display logic
    document.querySelectorAll('.tab-content').forEach(sec => sec.classList.add('hidden'));
    document.querySelectorAll('.nav-btn').forEach(btn => btn.classList.remove('active'));

    document.getElementById(sectionID).classList.remove('hidden');
    event.currentTarget.classList.add('active');
    }

    // search for logs
    document.addEventListener('DOMContentLoaded', () =>{
        const search = document.getElementById('logSearch');

        if(!search) return;

        search.addEventListener('keyup',function(){
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll('#logs-container tbody tr');

        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '': 'none';
        });
    });
    });

    document.addEventListener('DOMContentLoaded', ()=>{
        const adminSearch = document.getElementById('search');
        const clearBtn = document.getElementById('clear-btn');
        const tablerRows = document.querySelectorAll('#adminlist tbody tr');

        if(adminSearch){
            adminSearch.addEventListener('keyup', function(){
                const query = this.value.toLowerCase();

                tablerRows.forEach(row=> {
                    
                    const email = row.cells[0].textContent.toLowerCase();
                    
                    if(email.includes(query)){
                        row.style.display = "";
                    }else{
                        row.style.display= "none";
                    }
                });
            });
        }

        if(clearBtn){
            clearBtn.addEventListener('click', () =>{
                adminSearch.value = "";
                tablerRows.forEach(row => row.style.display = "");
            });
        }

    });
    // restore-btn
    document.addEventListener('click', function(e) {
    const restoreBtn = e.target.closest('.btn-restore');

    if (restoreBtn) {
        e.preventDefault(); 

        const url = restoreBtn.getAttribute('href');

        fetch(url)
            .then(res => res.json())
            .then(data => {
                showToast(data.message, data.status);
                if (data.status === 'success') {
                    const row = restoreBtn.closest('tr');
                    if (row) {
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 500);
                    }
                }
            })
            .catch(err => {
                console.error("Fetch error:", err);
                showToast("System error occurred.", "danger");
            });
    }
});

