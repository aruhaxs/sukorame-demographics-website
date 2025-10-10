document.addEventListener('DOMContentLoaded', () => {
    const carouselContainer = document.querySelector('.data-cards-container');
    const prevButton = document.querySelector('.carousel-nav.prev');
    const nextButton = document.querySelector('.carousel-nav.next');

    if (!carouselContainer || !prevButton || !nextButton) {
        return;
    }

    const cards = Array.from(carouselContainer.children);
    let currentIndex = 0;

    function updateCarousel() {
        cards.forEach((card, index) => {
            card.classList.remove('active', 'prev-1', 'prev-2', 'next-1', 'next-2');

            let position = index - currentIndex;

            if (position === 0) {
                card.classList.add('active');
            } else if (position === -1 || (position === cards.length - 1 && currentIndex === 0)) {
                card.classList.add('prev-1');
            } else if (position === 1 || (position === -(cards.length - 1) && currentIndex === cards.length - 1)) {
                card.classList.add('next-1');
            } else if (position === -2 || (position === cards.length - 2 && currentIndex <= 1)) {
                card.classList.add('prev-2');
            } else if (position === 2 || (position === -(cards.length - 2) && currentIndex >= cards.length - 2)) {
                card.classList.add('next-2');
            }
        });
    }

    prevButton.addEventListener('click', () => {
        currentIndex = (currentIndex > 0) ? currentIndex - 1 : cards.length - 1;
        updateCarousel();
    });

    nextButton.addEventListener('click', () => {
        currentIndex = (currentIndex < cards.length - 1) ? currentIndex + 1 : 0;
        updateCarousel();
    });

    // Panggil updateCarousel() pertama kali untuk inisialisasi
    updateCarousel();
});
