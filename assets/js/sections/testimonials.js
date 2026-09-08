document.addEventListener('DOMContentLoaded', () => {
	const testimonialSections = document.querySelectorAll('.sarathi-testimonials');

	testimonialSections.forEach(section => {
		const track = section.querySelector('.sarathi-testimonials-track');
		const cards = section.querySelectorAll('.sarathi-testimonials-card');
		const prevBtn = section.querySelector('.sarathi-testimonials-arrow-prev');
		const nextBtn = section.querySelector('.sarathi-testimonials-arrow-next');
		const pagination = section.querySelector('.sarathi-testimonials-pagination');

		if (!track || cards.length === 0) return;

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
				const cardWidth = cards[0].offsetWidth + parseInt(window.getComputedStyle(track).gap || 0);
				track.scrollBy({ left: -cardWidth, behavior: 'smooth' });
			});
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', () => {
				const cardWidth = cards[0].offsetWidth + parseInt(window.getComputedStyle(track).gap || 0);
				track.scrollBy({ left: cardWidth, behavior: 'smooth' });
			});
		}

		// Update active states on scroll
		let isScrolling;
		track.addEventListener('scroll', () => {
			window.clearTimeout(isScrolling);
			
			isScrolling = setTimeout(() => {
				const trackCenter = track.scrollLeft + track.clientWidth / 2;
				let closestIndex = 0;
				let minDistance = Infinity;

				cards.forEach((card, index) => {
					const cardCenter = card.offsetLeft + card.clientWidth / 2;
					const distance = Math.abs(trackCenter - cardCenter);
					
					if (distance < minDistance) {
						minDistance = distance;
						closestIndex = index;
					}
				});

				// Update classes
				cards.forEach(c => c.classList.remove('is-active'));
				dots.forEach(d => d.classList.remove('is-active'));

				if (cards.length > 2 && cards[closestIndex]) cards[closestIndex].classList.add('is-active');
				if (dots[closestIndex]) dots[closestIndex].classList.add('is-active');

			}, 100); // Debounce scroll event
		});

		// Initialize active state based on initial scroll position (usually index 0 or 1 depending on layout)
		setTimeout(() => {
			track.dispatchEvent(new Event('scroll'));
		}, 100);
	});
});
