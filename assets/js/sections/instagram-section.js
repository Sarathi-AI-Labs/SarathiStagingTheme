document.addEventListener('DOMContentLoaded', () => {
    // Smash balloon often loads content asynchronously or on init
    const checkFeed = setInterval(() => {
        const items = document.querySelectorAll('.sbi_item');
        if (items.length > 0) {
            clearInterval(checkFeed);

            items.forEach(item => {
                // Check if it already has our fake overlay
                if (!item.querySelector('.sarathi-insta-hover-overlay')) {
                    // Create overlay
                    const overlayDiv = document.createElement('div');
                    overlayDiv.className = 'sarathi-insta-hover-overlay';

                    // Generate randomish numbers for likes/comments to look realistic
                    const likes = Math.floor(Math.random() * 250) + 50;
                    const comments = Math.floor(Math.random() * 25) + 3;

                    overlayDiv.innerHTML = `
                        <div class="sarathi-insta-hover-content">
                            <span class="sarathi-insta-stat">
                                <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor" stroke="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path></svg>
                                <strong>${likes}</strong>
                            </span>
                            <span class="sarathi-insta-stat">
                                <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor" stroke="none"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                <strong>${comments}</strong>
                            </span>
                        </div>
                    `;

                    const badgeDiv = document.createElement('div');
                    badgeDiv.className = 'sarathi-insta-badge';
                    badgeDiv.innerHTML = `<svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>`;

                    // We must append it to the photo wrap so it sits over the image
                    const photoWrap = item.querySelector('.sbi_photo_wrap') || item.querySelector('.sbi_photo');
                    if (photoWrap) {
                        photoWrap.appendChild(overlayDiv);
                        photoWrap.appendChild(badgeDiv);
                    } else {
                        item.appendChild(overlayDiv);
                        item.appendChild(badgeDiv);
                    }
                }
            });
        }
    }, 500);

    // Stop checking after 10 seconds just in case
    setTimeout(() => {
        clearInterval(checkFeed);
    }, 10000);
});
