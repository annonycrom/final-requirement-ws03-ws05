document.addEventListener('DOMContentLoaded', () =>{
    const modal = document.getElementById("descModal");
    const closeBtn = document.querySelector(".close-modal");
    const modalTitle = document.getElementById("modalTitle");
    const modalDesc = document.getElementById("modalFullDesc");
    const suggestModal = document.getElementById("suggestModal");
    const openSuggestModal = document.getElementById('openSuggestModal');
    const closeSuggest = document.querySelector(".close-suggest");

    document.querySelectorAll('.view-details').forEach(button =>{
        button.addEventListener('click', ()=>{
            if(modalTitle && modalDesc){
                modalTitle.innerText = button.getAttribute('data-title');
                modalDesc.innerText = button.getAttribute('data-desc');
                modal.style.display = "flex";
            }
        }); 
    });
        
        if(openSuggestModal && suggestModal){
            openSuggestModal.addEventListener('click', () =>{
                suggestModal.style.display ="flex";
            });
        }

        closeBtn.onclick = () => modal.style.display = "none";

        closeSuggest.onclick = () => suggestModal.style.display = "none";


        window.onclick = (event) => {
            if (event.target == modal){
                modal.style.display = "none";
            }
            if(event.target == suggestModal){
                suggestModal.style.display = "none";
            }
        }   

    document.getElementById('image').onchange = function (){
        const container = document.querySelector('.form-file');
        const file = document.getElementById('file-name');
        const label = document.querySelector('.placeholder');
        
        if(this.files && this.files [0]){
            container.classList.add('active');
            file.textContent = "🗎 "+ this.files[0].name;
            label.textContent = "🗎 Sample Image";
        }else{
            container.classList.remove('active');
            file.textContent = "";
            label.textContent = "🗎 Upload Sample Image"
        }
    }
}) 