let logoutBtn = document.querySelector('.tab.logout');
let modalContainer = document.querySelector('.modal-container');
let timesCloseModal = document.querySelector('.modal-container .times');
let modalCloseBtn = document.querySelector('.modal-container .buttons .close');

timesCloseModal.addEventListener('click', function(){
    closeModal();
})

modalCloseBtn.addEventListener('click', function(){
    closeModal();
})

logoutBtn.addEventListener('click', function(){
    openModal();
})

function openModal(){
    modalContainer.classList.add('active');
}

function closeModal(){
    modalContainer.classList.remove('active');
}