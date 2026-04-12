   // reload prevent
    window.addEventListener('DOMContentLoaded', ()=>{
        const saved = localStorage.getItem('activeSection');
        if(saved){
            document.querySelectorAll('.tab-content').forEach(sec=>sec.classList.add('.hidden'));
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