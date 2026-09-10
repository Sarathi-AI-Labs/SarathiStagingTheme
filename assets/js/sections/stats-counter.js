document.addEventListener('DOMContentLoaded', function () {
    const statsSections = document.querySelectorAll('.cards-grid--stats');
    if (!statsSections.length) return;

    // Helper to format numbers with commas
    const formatNumber = (num) => {
        return Math.floor(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    };

    const animateValue = (obj, start, end, duration) => {
        let startTimestamp = null;
        
        // Extract original text to find suffix (e.g., +, M, %, K)
        const originalText = obj.getAttribute('data-original-text') || '';
        let suffix = '';
        const suffixMatch = originalText.match(/[^0-9.,]+$/);
        if (suffixMatch) {
            suffix = suffixMatch[0];
        }

        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            
            // easeOut easing function for a smooth slow-down at the end
            const easeOut = 1 - Math.pow(1 - progress, 4);
            const currentVal = (easeOut * (end - start) + start);
            
            obj.innerHTML = formatNumber(currentVal) + suffix;
            
            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                obj.innerHTML = formatNumber(end) + suffix; // Ensure exact end value
            }
        };
        
        window.requestAnimationFrame(step);
    };

    // Set up Intersection Observer to trigger when scrolled into view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const titleEls = entry.target.querySelectorAll('.cards-grid__item-title');
                
                titleEls.forEach(el => {
                    // Prevent re-animating if they scroll up and down
                    if (el.classList.contains('js-animated')) return;
                    
                    const originalText = el.textContent.trim();
                    el.setAttribute('data-original-text', originalText);
                    
                    // Extract just the number (removing commas, letters, symbols like '+' or '%')
                    const numString = originalText.replace(/[^0-9.]/g, '');
                    const endVal = parseFloat(numString);
                    
                    if (!isNaN(endVal) && endVal > 0) {
                        el.classList.add('js-animated');
                        
                        // Set text to 0 initially with the suffix
                        const suffixMatch = originalText.match(/[^0-9.,]+$/);
                        el.textContent = '0' + (suffixMatch ? suffixMatch[0] : '');
                        
                        // Start animation (duration 2500ms)
                        animateValue(el, 0, endVal, 2500);
                    }
                });
                
                // Stop observing this section once triggered
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    statsSections.forEach(section => {
        observer.observe(section);
    });
});
