// Script for client-side interactivity

// Confirmation before applying to an offer
document.addEventListener("DOMContentLoaded", () => {
    const applyButtons = document.querySelectorAll("form[action*='applyToOffer'] button");

    applyButtons.forEach((button) => {
        button.addEventListener("click", (e) => {
            if (!confirm("Êtes-vous sûr de vouloir postuler à cette offre ?")) {
                e.preventDefault();
            }
        });
    });
});
