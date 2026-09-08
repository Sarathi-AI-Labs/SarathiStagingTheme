document.addEventListener("DOMContentLoaded", () => {
    // Select all sections (supports both .why-choose-section and .sarathi-welcome)
    const sections = document.querySelectorAll(".why-choose-section, .sarathi-welcome");

    if (!sections.length) {
        return;
    }

    let ticking = false;

    const updateImages = () => {
        const isMobile = window.innerWidth < 1024;

        sections.forEach((section) => {
            const images = section.querySelectorAll(".why-choose-image, .sarathi-welcome-image img, .sarathi-imagetext-richtext-image");

            if (!images.length) {
                return;
            }

            // On mobile & tablet viewports, clear inline transform so CSS handles layout natively
            if (isMobile) {
                images.forEach((image) => {
                    image.style.transform = '';
                });
                return;
            }

            const rect = section.getBoundingClientRect();
            const viewportHeight = window.innerHeight;

            const progress = (viewportHeight - rect.top) / (viewportHeight + rect.height);
            const clampedProgress = Math.max(0, Math.min(1, progress));

            images.forEach((image) => {
                const container = image.closest(".why-choose-image-media, .why-choose-image-col, .sarathi-welcome-image, .sarathi-imagetext-richtext-image-col");
                const containerHeight = container ? container.clientHeight : 0;
                const imageHeight = image.clientHeight;

                if (containerHeight > 0 && imageHeight > containerHeight) {
                    const overflow = imageHeight - containerHeight;
                    const translateY = -overflow * (1 - clampedProgress);
                    image.style.transform = `translateY(${translateY.toFixed(2)}px)`;
                } else {
                    image.style.transform = '';
                }
            });
        });

        ticking = false;
    };

    const onScroll = () => {
        if (!ticking) {
            window.requestAnimationFrame(updateImages);
            ticking = true;
        }
    };

    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", () => {
        if (!ticking) {
            window.requestAnimationFrame(updateImages);
            ticking = true;
        }
    });

    updateImages();
});


