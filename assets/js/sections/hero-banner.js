/**
 * Hero Slider JavaScript
 * Standard vanilla JS slider with autoplay, touch swipe, and accessibility support.
 */

document.addEventListener('DOMContentLoaded', function () {
	const heroSliders = document.querySelectorAll('.sarathi-hero-slider');
	if (!heroSliders.length) return;

	heroSliders.forEach(function (heroSection) {
		const sliderTrack = heroSection.querySelector('.sarathi-slider-track');
		if (!sliderTrack) return;

		const slides = sliderTrack.querySelectorAll('.sarathi-slide');
		const prevBtn = heroSection.querySelector('.sarathi-slider-prev');
		const nextBtn = heroSection.querySelector('.sarathi-slider-next');
		const dotsContainer = heroSection.querySelector('.sarathi-slider-dots');
		const dots = dotsContainer ? dotsContainer.querySelectorAll('.sarathi-dot') : [];

		if (slides.length <= 1) {
			if (prevBtn) prevBtn.style.display = 'none';
			if (nextBtn) nextBtn.style.display = 'none';
			if (dotsContainer) dotsContainer.style.display = 'none';
			return;
		}

		let currentIndex = 0;
		let autoplayTimer = null;
		const intervalTime = 6000; // 6 seconds per slide

		function goToSlide(index) {
			slides.forEach((slide, i) => {
				if (i === index) {
					slide.classList.add('active');
				} else {
					slide.classList.remove('active');
				}
			});

			dots.forEach((dot, i) => {
				if (i === index) {
					dot.classList.add('active');
				} else {
					dot.classList.remove('active');
				}
			});

			currentIndex = index;
		}

		function nextSlide() {
			const nextIndex = (currentIndex + 1) % slides.length;
			goToSlide(nextIndex);
		}

		function prevSlide() {
			const prevIndex = (currentIndex - 1 + slides.length) % slides.length;
			goToSlide(prevIndex);
		}

		function startAutoplay() {
			stopAutoplay();
			autoplayTimer = setInterval(nextSlide, intervalTime);
		}

		function stopAutoplay() {
			if (autoplayTimer) {
				clearInterval(autoplayTimer);
				autoplayTimer = null;
			}
		}

		// Event Listeners for Prev/Next
		if (prevBtn) {
			prevBtn.addEventListener('click', function () {
				prevSlide();
				startAutoplay();
			});
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', function () {
				nextSlide();
				startAutoplay();
			});
		}

		// Event Listeners for Dots
		dots.forEach((dot, i) => {
			dot.addEventListener('click', function () {
				goToSlide(i);
				startAutoplay();
			});
		});

		// Pause autoplay on mouse hover
		heroSection.addEventListener('mouseenter', stopAutoplay);
		heroSection.addEventListener('mouseleave', startAutoplay);

		// Touch / Swipe support
		let startX = 0;
		let endX = 0;

		sliderTrack.addEventListener('touchstart', function (e) {
			startX = e.touches[0].clientX;
		}, { passive: true });

		sliderTrack.addEventListener('touchend', function (e) {
			endX = e.changedTouches[0].clientX;
			handleSwipe();
		}, { passive: true });

		function handleSwipe() {
			const threshold = 40; // minimum distance to count as swipe
			if (startX - endX > threshold) {
				nextSlide();
				startAutoplay();
			} else if (endX - startX > threshold) {
				prevSlide();
				startAutoplay();
			}
		}

		// Start autoplay initially
		startAutoplay();
	});
});
