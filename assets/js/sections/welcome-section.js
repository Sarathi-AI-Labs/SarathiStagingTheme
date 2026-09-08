document.addEventListener("DOMContentLoaded", () => {
    const section = document.querySelector(".sarathi-welcome");
    const image   = document.querySelector(".sarathi-welcome-image img");

    if (!section || !image) {
        return;
    }

    let ticking = false;

    const updateImage = () => {
        const isMobile = window.innerWidth < 1024;
        if (isMobile) {
            image.style.transform = '';
            return;
        }

        const rect = section.getBoundingClientRect();
        const viewportHeight = window.innerHeight;

        const progress = (viewportHeight - rect.top) / (viewportHeight + rect.height);
        const clampedProgress = Math.max(0, Math.min(1, progress));

        const container = image.closest(".sarathi-welcome-image");
        const containerHeight = container ? container.clientHeight : 0;
        const imageHeight = image.clientHeight;

        if (containerHeight > 0 && imageHeight > containerHeight) {
            const overflow = imageHeight - containerHeight;
            const translateY = -overflow * (1 - clampedProgress);
            image.style.transform = `translateY(${translateY.toFixed(2)}px)`;
        } else {
            image.style.transform = '';
        }

        ticking = false;
    };

    const onScroll = () => {
        if (!ticking) {
            window.requestAnimationFrame(updateImage);
            ticking = true;
        }
    };

    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", () => {
        if (!ticking) {
            window.requestAnimationFrame(updateImage);
            ticking = true;
        }
    });

    updateImage();
});