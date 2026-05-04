   // reload prevent
    window.addEventListener('DOMContentLoaded', ()=>{
        const saved = localStorage.getItem('activeSection');
        if(saved){
            document.querySelectorAll('.tab-content').forEach(sec=>sec.classList.add('hidden'));
            document.getElementById(saved).classList.remove('hidden');

            document.querySelectorAll('.nav-btn').forEach(btn=>btn.classList.remove('active'));
            document.querySelector(`[onclick*="${saved}"]`)?.classList.add('active');
        }
    });

    // tab switching in the dashboard
    function showSection(event, sectionID){
        const sections = document.querySelectorAll('.tab-content');
        sections.forEach(sec=>sec.classList.add('hidden'));

        const buttons = document.querySelectorAll('.nav-btn');
        buttons.forEach(btn=>btn.classList.remove('active'));

        document.getElementById(sectionID).classList.remove('hidden');

        event.currentTarget.classList.add('active');

        localStorage.setItem('activeSection', sectionID);
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

