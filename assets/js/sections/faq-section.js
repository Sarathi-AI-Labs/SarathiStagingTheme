/**
 * FAQ Section Accordion Component
 * Multi-instance scoped accordion behavior
 */
document.addEventListener('DOMContentLoaded', function () {
    const faqSections = document.querySelectorAll('.sarathi-faq-section');

    faqSections.forEach(function (section) {
        const items = section.querySelectorAll('.sarathi-faq-item');

        items.forEach(function (item) {
            const button = item.querySelector('.sarathi-faq-question');
            const answer = item.querySelector('.sarathi-faq-answer');

            if (!button || !answer) return;

            button.addEventListener('click', function () {
                const isOpen = item.classList.contains('is-active');

                // Close other items in the SAME section only (scoped accordion)
                items.forEach(function (otherItem) {
                    if (otherItem !== item) {
                        const otherBtn = otherItem.querySelector('.sarathi-faq-question');
                        const otherAns = otherItem.querySelector('.sarathi-faq-answer');

                        otherItem.classList.remove('is-active');
                        if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
                        if (otherAns) otherAns.hidden = true;
                    }
                });

                // Toggle the clicked item
                if (isOpen) {
                    item.classList.remove('is-active');
                    button.setAttribute('aria-expanded', 'false');
                    answer.hidden = true;
                } else {
                    item.classList.add('is-active');
                    button.setAttribute('aria-expanded', 'true');
                    answer.hidden = false;
                }
            });
        });
    });
});
