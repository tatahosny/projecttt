document.addEventListener('DOMContentLoaded', () => {
    const cartCount = document.getElementById('cart-count');
    let count = 0;

    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', () => {
            count++;
            cartCount.textContent = count;
        });
    });
});
