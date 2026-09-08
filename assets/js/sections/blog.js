/**
 * Blog Archive & Single Post JavaScript Behaviors
 *
 * @package Custom_Theme
 */

document.addEventListener('DOMContentLoaded', function () {
	// Category Select Dropdown Auto-Submit
	const catSelect = document.getElementById('blog-cat-select');
	const archiveCatSelect = document.getElementById('blog-archive-cat-select');

	function handleCategoryChange(event) {
		const form = event.target.closest('form');
		if (form) {
			form.submit();
		}
	}

	if (catSelect) {
		catSelect.addEventListener('change', handleCategoryChange);
	}

	if (archiveCatSelect) {
		archiveCatSelect.addEventListener('change', handleCategoryChange);
	}

	// Copy Article Link to Clipboard with Tooltip Feedback
	const copyBtns = document.querySelectorAll('.sarathi-share-copy-btn');
	copyBtns.forEach(function (btn) {
		btn.addEventListener('click', function (e) {
			e.preventDefault();
			const url = btn.getAttribute('data-url') || window.location.href;
			const tooltip = btn.querySelector('.sarathi-copy-tooltip');

			if (navigator.clipboard && window.isSecureContext) {
				navigator.clipboard.writeText(url).then(showTooltip).catch(fallbackCopy);
			} else {
				fallbackCopy();
			}

			function fallbackCopy() {
				const tempInput = document.createElement('input');
				tempInput.value = url;
				document.body.appendChild(tempInput);
				tempInput.select();
				document.execCommand('copy');
				document.body.removeChild(tempInput);
				showTooltip();
			}

			function showTooltip() {
				if (tooltip) {
					tooltip.classList.add('is-active');
					setTimeout(function () {
						tooltip.classList.remove('is-active');
					}, 2000);
				}
			}
		});
	});
});
