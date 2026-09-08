document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('.cards-grid--stats .cards-grid__item-title, .cards-grid--minimal .cards-grid__item-title');
    
    if (!counters.length) return;

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const text = el.innerText.trim();
                
                // Match number and optional prefix/suffix (e.g. 100+, $500, 95%)
                // The regex captures:
                // 1: Prefix (non-digits)
                // 2: The number itself
                // 3: Suffix (non-digits)
                const match = text.match(/^([^\d]*)(\d+)([^\d]*)$/);
                
                if (match) {
                    const prefix = match[1];
                    const targetNum = parseInt(match[2], 10);
                    const suffix = match[3];
                    
                    let currentNum = 0;
                    const duration = 2000; // 2 seconds
                    const frameRate = 30; // ms per frame
                    const increment = targetNum / (duration / frameRate);
                    
                    el.innerText = `${prefix}0${suffix}`;
                    
                    const interval = setInterval(() => {
                        currentNum += increment;
                        if (currentNum >= targetNum) {
                            el.innerText = `${prefix}${targetNum}${suffix}`;
                            clearInterval(interval);
                        } else {
                            el.innerText = `${prefix}${Math.floor(currentNum)}${suffix}`;
                        }
                    }, frameRate);
                }
                
                // Unobserve after animating once
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => {
        observer.observe(counter);
    });
});
