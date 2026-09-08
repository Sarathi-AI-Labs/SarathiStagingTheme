document.addEventListener('DOMContentLoaded', function () {
    const sections = document.querySelectorAll('.cards-grid--standard');

    sections.forEach(function (section) {
        const cards = section.querySelectorAll('.js-extend-service-card');
        if (!cards.length) return;

        const sharedDetails = section.querySelector('.cards-grid__shared-details');
        if (!sharedDetails) return;

        const titleEl = sharedDetails.querySelector('.js-shared-title');
        const descEl = sharedDetails.querySelector('.js-shared-desc');
        const imageEl = sharedDetails.querySelector('.js-shared-image');
        const featuresEl = sharedDetails.querySelector('.js-shared-features');
        const eyebrowEl = sharedDetails.querySelector('.js-shared-eyebrow');
        const btnWrapperEl = sharedDetails.querySelector('.js-shared-btn-wrapper');
        const btnEl = sharedDetails.querySelector('.js-shared-btn');

        function populateDetails(card) {
            const dataStr = card.getAttribute('data-details');
            if (!dataStr) return;

            try {
                const data = JSON.parse(dataStr);

                // Populate Text
                if (eyebrowEl) {
                    if (data.eyebrow) {
                        eyebrowEl.textContent = data.eyebrow;
                        eyebrowEl.style.display = 'inline-block';
                    } else {
                        eyebrowEl.style.display = 'none';
                    }
                }
                if (titleEl) titleEl.textContent = data.heading || '';
                if (descEl) descEl.innerHTML = data.description ? `<p>${data.description}</p>` : '';

                // Populate Image
                if (imageEl && data.image) {
                    imageEl.src = data.image;
                    imageEl.alt = data.heading || '';
                    imageEl.parentElement.style.display = 'block';
                } else if (imageEl) {
                    imageEl.parentElement.style.display = 'none';
                }

                // Populate Features
                if (featuresEl) {
                    featuresEl.innerHTML = '';
                    if (data.features && data.features.length) {
                        data.features.forEach(function (feature) {
                            const featDiv = document.createElement('div');
                            featDiv.className = 'cards-grid__shared-feature';

                            if (feature.icon) {
                                const featIconWrap = document.createElement('div');
                                featIconWrap.className = 'cards-grid__shared-feature-icon';
                                const featIcon = document.createElement('img');
                                featIcon.src = feature.icon;
                                featIcon.alt = '';
                                featIconWrap.appendChild(featIcon);
                                featDiv.appendChild(featIconWrap);
                            }

                            const featTextWrap = document.createElement('div');
                            featTextWrap.className = 'cards-grid__shared-feature-text';

                            const featTitle = document.createElement('h4');
                            featTitle.className = 'cards-grid__shared-feature-title';
                            featTitle.textContent = feature.title || '';

                            const featDesc = document.createElement('p');
                            featDesc.className = 'cards-grid__shared-feature-desc';
                            featDesc.textContent = feature.desc || '';

                            featTextWrap.appendChild(featTitle);
                            featTextWrap.appendChild(featDesc);
                            featDiv.appendChild(featTextWrap);
                            
                            featuresEl.appendChild(featDiv);
                        });
                        featuresEl.style.display = 'grid';
                    } else {
                        featuresEl.style.display = 'none';
                    }
                }

                // Populate Button
                if (btnWrapperEl && btnEl) {
                    if (data.button && data.button.url) {
                        btnEl.href = data.button.url;
                        btnEl.textContent = data.button.title || 'Explore Service';
                        btnEl.target = data.button.target || '_self';
                        btnWrapperEl.style.display = 'block';
                    } else {
                        btnWrapperEl.style.display = 'none';
                    }
                }

                sharedDetails.style.display = 'block';

            } catch (e) {
                console.error('Error parsing card details:', e);
            }
        }

        function setActiveCard(targetCard) {
            cards.forEach(c => c.classList.remove('is-active'));
            targetCard.classList.add('is-active');
            populateDetails(targetCard);
        }

        // Initialize first card
        setActiveCard(cards[0]);

        // Add click events
        cards.forEach(function (card) {
            const btn = card.querySelector('.js-explore-service');
            const trigger = btn ? btn : card;

            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                setActiveCard(card);
                
                // Smooth scroll to details
                const headerOffset = 80; // Adjust for sticky header if needed
                const elementPosition = sharedDetails.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            });
        });
    });
});
