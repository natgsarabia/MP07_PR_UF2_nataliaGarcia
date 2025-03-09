window.onload = () => {
    console.log("Script cargado correctamente");
    
    let buttonNewUser = document.getElementById('newUser');
    if(buttonNewUser) {
        console.log("Botón de usuario encontrado");
        buttonNewUser.addEventListener('click', registrarUsuari);
    } else {
        console.error("Botón de usuario no encontrado");
    }

    let buttonAnuncis = document.getElementById('newAnunci');
    if(buttonAnuncis) {
        console.log("Botón de anuncio encontrado");
        buttonAnuncis.addEventListener('click', veureAnuncis);
    } else {
        console.error("Botón de anuncio no encontrado");
    }

    const leftButton = document.querySelector('.arrow.left');
    const rightButton = document.querySelector('.arrow.right');
    const carouselContainer = document.querySelector('.carousel-container');
    const cardContainers = carouselContainer.querySelectorAll('.card-container');
    const MAX_VISIBILITY = 3; 
    let activeIndex = 0;

    const updateCarousel = () => {
        cardContainers.forEach((container, index) => {
            const offset = activeIndex - index; 
            const absOffset = Math.abs(offset);

            if (absOffset === 0) {
                container.classList.add('active');
                container.style.transform = 'scale(1)';
                container.style.filter = 'none'; 
            } else {
                container.classList.remove('active');
                container.style.transform = `scale(0.8)`; 
                container.style.filter = 'blur(3px)'; 
            }

            if (absOffset >= MAX_VISIBILITY) {
                container.style.display = 'none';
            } else {
                container.style.display = 'block';
            }
        });
    };


    const moveCarousel = (direction) => {
        const totalCards = cardContainers.length;
        activeIndex = (activeIndex + direction + totalCards) % totalCards; 
        updateCarousel(); 
    };


    leftButton.addEventListener('click', () => moveCarousel(-1));


    rightButton.addEventListener('click', () => moveCarousel(1));

    updateCarousel();

};

function registrarUsuari (){
    window.location.href ="../views/paginaAfegirUsuari.php";
}

function veureAnuncis (){
    window.location.href ="../views/paginaAfegirAnunci.php";
}

