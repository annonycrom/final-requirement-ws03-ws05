function showSection(sectionID){
    const sections = document.querySelectorAll('.tab-content');
    sections.forEach(sec=>sec.classList.add('hidden'));

    const buttons = document.querySelectorAll('.nav-btn');
    buttons.forEach(btn=>btn.classList.remove('active'));

    document.getElementById(sectionID).classList.remove('hidden');

    event.currentTarget.classList.add('active');
}