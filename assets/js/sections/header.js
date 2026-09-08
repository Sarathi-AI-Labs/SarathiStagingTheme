/**
 * Header Navigation & Mobile Burger Menu JavaScript
 */
document.addEventListener('DOMContentLoaded', function () {
	var headerToggle = document.querySelector('.sarathi-header-toggle');
	var headerNavContainer = document.querySelector('.sarathi-header-nav-container');
	var header = document.querySelector('.sarathi-header');

	if (!headerToggle || !headerNavContainer) {
		return;
	}

	function openMenu() {
		headerToggle.classList.add('is-active');
		headerToggle.setAttribute('aria-expanded', 'true');
		headerNavContainer.classList.add('is-open');
		if (header) {
			header.classList.add('is-nav-open');
		}
	}

	function closeMenu() {
		headerToggle.classList.remove('is-active');
		headerToggle.setAttribute('aria-expanded', 'false');
		headerNavContainer.classList.remove('is-open');
		if (header) {
			header.classList.remove('is-nav-open');
		}
	}

	function toggleMenu() {
		var isExpanded = headerToggle.getAttribute('aria-expanded') === 'true';
		if (isExpanded) {
			closeMenu();
		} else {
			openMenu();
		}
	}

	headerToggle.addEventListener('click', function (e) {
		e.stopPropagation();
		toggleMenu();
	});

	// Submenu toggle for touch/mobile devices
	var menuItemsWithChildren = headerNavContainer.querySelectorAll('.menu-item-has-children, .page_item_has_children');
	menuItemsWithChildren.forEach(function (item) {
		var link = item.querySelector(':scope > a');
		if (!link) return;

		// Create a dropdown indicator toggle button if sub-menu exists
		var dropdownToggle = document.createElement('button');
		dropdownToggle.type = 'button';
		dropdownToggle.className = 'sarathi-submenu-toggle';
		dropdownToggle.setAttribute('aria-label', 'Toggle Submenu');
		dropdownToggle.innerHTML = '<i class="fa-solid fa-chevron-down" aria-hidden="true"></i>';

		link.after(dropdownToggle);

		dropdownToggle.addEventListener('click', function (e) {
			e.preventDefault();
			e.stopPropagation();
			var parentLi = this.closest('.menu-item-has-children, .page_item_has_children');
			if (parentLi) {
				parentLi.classList.toggle('sub-menu-open');
				var isOpen = parentLi.classList.contains('sub-menu-open');
				this.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			}
		});
	});

	// Close menu when clicking outside
	document.addEventListener('click', function (e) {
		if (header && !header.contains(e.target) && headerNavContainer.classList.contains('is-open')) {
			closeMenu();
		}
	});

	// Close menu on Escape key press
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && headerNavContainer.classList.contains('is-open')) {
			closeMenu();
			headerToggle.focus();
		}
	});

	// Reset menu on resize to desktop screens
	window.addEventListener('resize', function () {
		if (window.innerWidth >= 992 && headerNavContainer.classList.contains('is-open')) {
			closeMenu();
		}
	});

	// Smart Hide/Show Header on Scroll
	var lastScrollY = window.scrollY;
	var scrollThreshold = 8;

	window.addEventListener('scroll', function () {
		if (!header) return;

		// Do not hide header if mobile menu drawer is currently open
		if (headerNavContainer && headerNavContainer.classList.contains('is-open')) {
			header.classList.remove('is-hidden');
			return;
		}

		var currentScrollY = window.scrollY;

		// Toggle transparent vs solid header based on scroll position
		if (currentScrollY > 40) {
			header.classList.add('is-scrolled');
		} else {
			header.classList.remove('is-scrolled');
		}

		// Always keep header visible at the top of the page
		if (currentScrollY <= 20) {
			header.classList.remove('is-hidden');
			lastScrollY = currentScrollY;
			return;
		}

		// Throttle minor scroll jitter
		if (Math.abs(currentScrollY - lastScrollY) < scrollThreshold) {
			return;
		}

		if (currentScrollY > lastScrollY && currentScrollY > 70) {
			// Scrolling DOWN -> hide header
			header.classList.add('is-hidden');
		} else if (currentScrollY < lastScrollY) {
			// Scrolling UP -> reveal header
			header.classList.remove('is-hidden');
		}

		lastScrollY = currentScrollY;
	}, { passive: true });
});
