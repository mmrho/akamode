document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const trigger = item.querySelector('.faq-trigger');
        const content = item.querySelector('.faq-content');

        trigger.addEventListener('click', () => {
            const isOpen = item.classList.contains('active');

            // 1. Close all other items first (True Accordion behavior)
            // If you want multiple items open at once, remove this specific block.
            faqItems.forEach(otherItem => {
                if (otherItem !== item && otherItem.classList.contains('active')) {
                    otherItem.classList.remove('active');
                    otherItem.querySelector('.faq-trigger').setAttribute('aria-expanded', 'false');
                    otherItem.querySelector('.faq-content').setAttribute('aria-hidden', 'true');
                }
            });

            // 2. Toggle the clicked item
            if (isOpen) {
                // Close it
                item.classList.remove('active');
                trigger.setAttribute('aria-expanded', 'false');
                content.setAttribute('aria-hidden', 'true');
            } else {
                // Open it
                item.classList.add('active');
                trigger.setAttribute('aria-expanded', 'true');
                content.setAttribute('aria-hidden', 'false');
            }
        });
    });
});