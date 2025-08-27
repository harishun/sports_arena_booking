const cards = document.querySelectorAll('.card');
cards.forEach(card => {
    card.addEventListener('click', () => {card.style.display = 'none';})})