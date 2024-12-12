const menuBtn = document.getElementById('menuBtn');
const menu = document.getElementById('menu');
const menuText = document.querySelectorAll('.menuText');

// Alternar o menu ao clicar no botão
menuBtn.addEventListener('click', () => {
    menu.classList.toggle('open'); // Alterna o estado aberto/fechado
    menuText.forEach((text, index) => {
        setTimeout(() => {
            text.classList.toggle('open2'); // Alterna a classe de texto com atraso
        }, index * 50);
    });
});


const dayNight = document.querySelector('#themeChangeBtn');
dayNight.addEventListener('click', () => {
    document.body.classList.toggle('dark');
    if(document.body.classList.contains('dark')){
        localStorage.setItem('theme', 'dark');
    }else {
        localStorage.setItem('theme','light');
    }
    updateIcon();
})

function themeMode() {
    if(localStorage.getItem('theme') !== null){
        if(localStorage.getItem('theme') === 'light'){
            document.body.classList.remove('dark');
        }else {
            document.body.classList.add('dark');
        }
    }
    updateIcon();
}
themeMode();

function updateIcon() {
    if(document.body.classList.contains('dark')){
        dayNight.querySelector('i').classList.remove('fa-moon');
        dayNight.querySelector('i').classList.add('fa-sun');
    } else {
        dayNight.querySelector('i').classList.remove('fa-sun');
        dayNight.querySelector('i').classList.add('fa-moon');
    }
}

const btnSair = document.querySelector('#btnSair');
btnSair.addEventListener('click', ()=> {
    window.location.href = "/open_eventos/login/sair.php";
})