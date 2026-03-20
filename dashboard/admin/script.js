
function showSection(event, sectionID){
    const sections = document.querySelectorAll('.tab-content');
    sections.forEach(sec=>sec.classList.add('hidden'));

    const buttons = document.querySelectorAll('.nav-btn');
    buttons.forEach(btn=>btn.classList.remove('active'));

    document.getElementById(sectionID).classList.remove('hidden');

    event.currentTarget.classList.add('active');
}

function toggleEdit(btn){
    const row = btn.closest('tr');
    const descInput = row.querySelector('.desc-in');
    const input =row.querySelectorAll('.edit-input');
    const hashedId = row.getAttribute('data-id');
    const viewBtn = row.querySelector(".btn-view-cancel");

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

        .then(response => response.text())
        .then(result => {
            alert("Successfullt Updated!");

            input.forEach(input=>{
                input.readOnly = true;
                input.classList.remove('editable');
            });
            
            
        descInput.oninput = null;
        descInput.style.height = "35px";
    
        btn.textContent ="Edit";
        viewBtn.textContent = "View";
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
    else{
        if(descInput.style.height === "auto" || descInput.style.height > "35px"){
            descInput.style.height = "35px";
            btn.textContent = "View";
        }else{
            descInput.style.height = "auto";
            descInput.style.height = descInput.scrollHeight + "px";
            btn.textContent = "Close";
        }
    }
}