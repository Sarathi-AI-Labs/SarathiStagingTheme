document.addEventListener('DOMContentLoaded', () => {
	const testimonialSections = document.querySelectorAll('.sarathi-testimonials');

	testimonialSections.forEach(section => {
		const track = section.querySelector('.sarathi-testimonials-track');
		const cards = section.querySelectorAll('.sarathi-testimonials-card');
		const prevBtn = section.querySelector('.sarathi-testimonials-arrow-prev');
		const nextBtn = section.querySelector('.sarathi-testimonials-arrow-next');
		const pagination = section.querySelector('.sarathi-testimonials-pagination');

		if (!track || cards.length === 0) return;

		let currentIndex = 0;

		// Create pagination dots
		cards.forEach((_, index) => {
			const dot = document.createElement('button');
			dot.classList.add('sarathi-testimonials-dot');
			dot.setAttribute('aria-label', `Go to testimonial ${index + 1}`);
			if (index === 1 && cards.length > 2) dot.classList.add('is-active'); // Default active
			
			dot.addEventListener('click', () => {
				scrollToCard(index);
			});
			
			pagination.appendChild(dot);
		});

		const dots = pagination.querySelectorAll('.sarathi-testimonials-dot');

		// Scroll to specific card
		function scrollToCard(index) {
			if (index < 0 || index >= cards.length) return;
			
			if (cards[index]) {
				const trackRect = track.getBoundingClientRect();
				const cardRect = cards[index].getBoundingClientRect();
				// Calculate position to center the card
				const scrollLeft = cards[index].offsetLeft - (trackRect.width / 2) + (cardRect.width / 2);
				
				track.scrollTo({
					left: scrollLeft,
					behavior: 'smooth'
				});
			}
		}

		// Arrow clicks
		if (prevBtn) {
			prevBtn.addEventListener('click', () => {
				scrollToCard(currentIndex - 1);
			});
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', () => {
				scrollToCard(currentIndex + 1);
			});
		}

		function updateArrowState() {
			const atStart = track.scrollLeft <= 10;
			const atEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - 10;
			
			if (prevBtn) {
				if (atStart) prevBtn.classList.add('is-disabled');
				else prevBtn.classList.remove('is-disabled');
			}
			
			if (nextBtn) {
				if (atEnd) nextBtn.classList.add('is-disabled');
				else nextBtn.classList.remove('is-disabled');
			}
		}

		// Update active states on scroll
		let isScrolling;
		let isFirstRun = true;
		track.addEventListener('scroll', () => {
			window.clearTimeout(isScrolling);
			
			updateArrowState();
			
			// Update active dot and card fast
			const trackCenter = track.scrollLeft + track.clientWidth / 2;
			let minDistance = Infinity;
			let closest = 0;

			cards.forEach((card, index) => {
				const cardCenter = card.offsetLeft + card.clientWidth / 2;
				const distance = Math.abs(trackCenter - cardCenter);
				
				if (distance < minDistance) {
					minDistance = distance;
					closest = index;
				}
			});
			
			if (currentIndex !== closest || isFirstRun) {
			    currentIndex = closest;
			    isFirstRun = false;
			    
			    // Update classes
				cards.forEach(c => c.classList.remove('is-active'));
				dots.forEach(d => d.classList.remove('is-active'));

				if (cards[currentIndex]) cards[currentIndex].classList.add('is-active');
				if (dots[currentIndex]) dots[currentIndex].classList.add('is-active');
			}
			
			isScrolling = setTimeout(() => {
			    // Optionally snap to closest after scrolling stops, but native scroll-snap handles this
			}, 100);
		});

		// Initialize active state
		setTimeout(() => {
			track.dispatchEvent(new Event('scroll'));
			checkTextTruncation();
			updateArrowState();
		}, 100);

		// Modal Logic
		const modal = section.querySelector('.sarathi-testimonials-modal');
		const modalOverlay = section.querySelector('.sarathi-testimonials-modal-overlay');
		const modalClose = section.querySelector('.sarathi-testimonials-modal-close');
		const modalText = section.querySelector('.sarathi-testimonials-modal-text');
		const modalAuthor = section.querySelector('.sarathi-testimonials-modal-author');
		const modalPhotoWrapper = section.querySelector('.sarathi-testimonials-modal-photo-wrapper');

		function checkTextTruncation() {
			cards.forEach(card => {
				const p = card.querySelector('.sarathi-testimonials-quote p');
				if (!p) return;
				
				// A small tolerance (e.g. 2px) handles fractional pixel issues in some browsers
				if (p.scrollHeight > p.clientHeight + 2) {
					card.classList.add('is-clickable');
				} else {
					card.classList.remove('is-clickable');
				}
			});
		}

		window.addEventListener('resize', () => {
			checkTextTruncation();
			updateArrowState();
		});

		cards.forEach(card => {
			card.addEventListener('click', () => {
				if (!modal || !card.classList.contains('is-clickable')) return;
				
				// Populate Modal
				const fullText = card.getAttribute('data-full-text');
				const photo = card.querySelector('.sarathi-testimonials-photo');
				const author = card.querySelector('.sarathi-testimonials-author');
				
				modalText.textContent = fullText;
				
				modalPhotoWrapper.innerHTML = '';
				if (photo) modalPhotoWrapper.appendChild(photo.cloneNode(true));
				
				modalAuthor.innerHTML = '';
				if (author) modalAuthor.appendChild(author.cloneNode(true));
				
				// Open Modal
				modal.classList.add('is-open');
				document.body.style.overflow = 'hidden';
			});
		});

		function closeModal() {
			if (modal) {
				modal.classList.remove('is-open');
				document.body.style.overflow = '';
			}
		}

		if (modalClose) modalClose.addEventListener('click', closeModal);
		if (modalOverlay) modalOverlay.addEventListener('click', closeModal);
		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape' && modal && modal.classList.contains('is-open')) closeModal();
		});
	});
});
