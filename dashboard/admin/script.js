
    // Handle tab switching in the dashboard
    function showSection(event, sectionID){
        const sections = document.querySelectorAll('.tab-content');
        sections.forEach(sec=>sec.classList.add('hidden'));

        const buttons = document.querySelectorAll('.nav-btn');
        buttons.forEach(btn=>btn.classList.remove('active'));

        document.getElementById(sectionID).classList.remove('hidden');

        event.currentTarget.classList.add('active');

        localStorage.setItem('activeSection', sectionID);
    }

    // fetching data from the server for approve, archive, and restore actions
    function performAction(url, btnElement){
        fetch(url)
        .then(response => response.json())
        .then(data => {
            showToast(data.message, data.status);

            if(data.status === "success" && btnElement){
                const row = btnElement.closest('tr');
                row.style.opacity = "0";
                setTimeout(() => row.remove(), 300);
            }
        })
        .catch(err => showToast("An error occurred. Please try again.", "error"));
    }
    // taost notification function
    function showToast(message, type){  
        const toast = document.getElementById("toast");
        toast.innerText = message;
        toast.className = `toast show ${type}`;
        setTimeout (() => toast.classList.remove("show"), 3000);
    }
    // restore last active section on page load

    document.addEventListener('DOMContentLoaded', () => {
        const savedSection = localStorage.getItem('activeSection');
        if(savedSection){
            const targetBtn = document.querySelector(`a[onclick*=${savedSection}]`);
            if (targetBtn){
                targetBtn.click();
            }
        }
    });

    // Toggle Edit and Save actions for items in the update section
    function toggleEdit(btn){
        const row = btn.closest('tr');
        const descInput = row.querySelector('.desc-in');
        const input =row.querySelectorAll('.edit-input');
        const hashedId = btn.getAttribute('data-id');
        const viewBtn = row.querySelector(".btn-view-cancel");
        // Swithching between Edit and Save mode
        if(btn.textContent === "Edit"){
            input.forEach(input=>{
                input.readOnly = false;
                input.classList.add('editable');
                input.setAttribute('data-original',input.value);
            });
            
            descInput.style.height = "auto";
            descInput.style.height = descInput.scrollHeight + "px";

            descInput.oninput = function(){
                this.style.height = "auto";
                this.style.height = this.scrollHeight + "px";
            }

            btn.textContent = "Save";
            viewBtn.textContent = "Cancel";
        }else{
            const data = {
                id: hashedId,
                name: row.querySelector('.name-in').value,
                desc: row.querySelector('.desc-in').value,
                price: row.querySelector('.price-in').value
            };

            fetch('edit-item.php',{
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            })

            .then(response => response.json())
            .then(result => {
                showToast(result.message || "Successfully Updated!", result.status || "success");

                input.forEach(input=>{
                    input.readOnly = true;
                    input.classList.remove('editable');
                });
                
                
            descInput.oninput = null;
            descInput.style.height = "35px";
            descInput.setAttribute('readonly', true);
        
            btn.textContent ="Edit";
            viewBtn.textContent = "View";
            })

            .catch(err => {
                showToast("An error occurred. Please try again.", "error")
            });
        }
    }

    function handleViewCancel(btn){
        const row = btn.closest('tr');
        const descInput = row.querySelector('.desc-in');
        const input =row.querySelectorAll('.edit-input');
        const editBtn = row.querySelector('.btn-approve');
        
        if(btn.textContent === "Cancel"){
            input.forEach(input =>{
                input.value = input.getAttribute('data-original');
                input.readOnly = true;
                input.classList.remove('editable');
            });


            descInput.oninput = null;
            descInput.style.height = "35px";

            editBtn.textContent = "Edit";
            btn.textContent = "View";
        }
        else if(btn.textContent === "View"){
            descInput.style.height = "auto";

            requestAnimationFrame(() => {
                descInput.style.height = descInput.scrollHeight + "px";
                btn.textContent ="Close";
            });
        }else{
            descInput.style.height = "35px";
            btn.textContent = "View";
        }
    }

    function filterStore(status){
        const card = document.querySelectorAll('.card');

        card.forEach(card => {
            const cardStatus = card.getAttribute('data-status');
            if(status === 'all' || cardStatus === status){
                card.style.display = "flex";
            }else{
                card.style.display = "none";
            }
        });
    }

    const modalTitle = document.getElementById("modalTitle");
    const modalDesc = document.getElementById("modalFullDesc");
    const modal = document.getElementById("descModal");
    const closeBtn = document.querySelector(".close-modal");

    
    document.querySelectorAll('.view-details').forEach(button =>{
        button.addEventListener('click', ()=>{
            if(modalTitle && modalDesc){
                modalTitle.innerText = button.getAttribute('data-title');
                modalDesc.innerText = button.getAttribute('data-desc');
                modal.style.display = "flex";
            }
        }); 
    });

    closeBtn.onclick = () => modal.style.display = "none";

    window.onclick = (event) => {
        if (event.target == modal){
            modal.style.display = "none";
        }
    }
