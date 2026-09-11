/**
 * Form Section JavaScript
 * Handles Contact Form 7 feedback message auto-dismissal:
 * - Auto-fades out and hides success message after 5 seconds
 * - Dismisses message if user clicks on the banner
 * - Dismisses message when user starts typing in any field to submit again
 * - Uses MutationObserver + standard CF7 events to reliably catch all submissions
 *
 * @package Custom_Theme
 */

(function () {
	'use strict';

	var DISMISS_TIMEOUT = 5000; // 5 seconds display time
	var FADE_TIME = 500;        // 500ms to match CSS transition

	/**
	 * Smoothly fade out and hide the response output element.
	 *
	 * @param {HTMLElement} responseEl
	 * @param {HTMLFormElement} form
	 */
	function hideMessage(responseEl, form) {
		if (!responseEl) {
			return;
		}

		if (responseEl._dismissTimer) {
			clearTimeout(responseEl._dismissTimer);
			responseEl._dismissTimer = null;
		}

		// Don't re-animate if already fading or hidden
		if (responseEl.classList.contains('sarathi-msg-fade-out') || responseEl.classList.contains('sarathi-msg-hidden')) {
			return;
		}

		responseEl.classList.add('sarathi-msg-fade-out');

		setTimeout(function () {
			responseEl.classList.add('sarathi-msg-hidden');
			responseEl.classList.remove('sarathi-msg-fade-out');

			if (form) {
				form.classList.remove('sent');
			}
		}, FADE_TIME);
	}

	/**
	 * Reset any fade or hidden classes on the response output element.
	 *
	 * @param {HTMLElement} responseEl
	 */
	function resetMessage(responseEl) {
		if (!responseEl) {
			return;
		}

		if (responseEl._dismissTimer) {
			clearTimeout(responseEl._dismissTimer);
			responseEl._dismissTimer = null;
		}

		responseEl.classList.remove('sarathi-msg-fade-out', 'sarathi-msg-hidden');
	}

	/**
	 * Schedule auto-dismiss for a form response element.
	 *
	 * @param {HTMLElement} responseEl
	 * @param {HTMLFormElement} form
	 */
	function scheduleDismiss(responseEl, form) {
		if (!responseEl) {
			return;
		}

		resetMessage(responseEl);

		responseEl._dismissTimer = setTimeout(function () {
			hideMessage(responseEl, form);
		}, DISMISS_TIMEOUT);
	}

	/**
	 * Watch response output element with MutationObserver so any text change
	 * by CF7 triggers the auto-dismiss timer when in sent state.
	 *
	 * @param {HTMLElement} responseEl
	 */
	function observeResponseOutput(responseEl) {
		if (!responseEl || responseEl._sarathiObserved) {
			return;
		}

		responseEl._sarathiObserved = true;

		var observer = new MutationObserver(function () {
			var text = (responseEl.innerText || responseEl.textContent || '').trim();
			var form = responseEl.closest('.wpcf7-form') || responseEl.closest('form');

			if (text.length > 0 && form && (form.classList.contains('sent') || form.getAttribute('data-status') === 'sent' || text.indexOf('Thank you') !== -1)) {
				scheduleDismiss(responseEl, form);
			}
		});

		observer.observe(responseEl, {
			childList: true,
			characterData: true,
			subtree: true
		});
	}

	function initObservers() {
		document.querySelectorAll('.wpcf7-response-output').forEach(function (el) {
			observeResponseOutput(el);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initObservers);
	} else {
		initObservers();
	}

	// 1. When a new submission starts: clear any old fade/hidden state
	document.addEventListener('wpcf7beforesubmit', function (e) {
		var form = e.target.tagName === 'FORM' ? e.target : (e.target.closest ? e.target.closest('form') : e.target);
		if (form) {
			var responseEl = form.querySelector('.wpcf7-response-output');
			resetMessage(responseEl);
		}
	});

	// 2. Contact Form 7: mail sent successfully
	document.addEventListener('wpcf7mailsent', function (e) {
		var form = e.target.tagName === 'FORM' ? e.target : (e.target.closest ? e.target.closest('form') : e.target);
		if (!form) return;
		var responseEl = form.querySelector('.wpcf7-response-output');
		if (responseEl) {
			scheduleDismiss(responseEl, form);
		}
	});

	// 3. Contact Form 7: general submit event backup
	document.addEventListener('wpcf7submit', function (e) {
		if (e.detail && e.detail.status === 'mail_sent') {
			var form = e.target.tagName === 'FORM' ? e.target : (e.target.closest ? e.target.closest('form') : e.target);
			if (!form) return;
			var responseEl = form.querySelector('.wpcf7-response-output');
			if (responseEl) {
				scheduleDismiss(responseEl, form);
			}
		}
	});

	// 4. Click to dismiss directly on the message banner
	document.addEventListener('click', function (e) {
		var responseEl = e.target.closest('.wpcf7-response-output');
		if (responseEl) {
			var form = responseEl.closest('.wpcf7-form');
			hideMessage(responseEl, form);
		}
	});

	// 5. Dismiss success message when user starts typing into any field
	document.addEventListener('input', function (e) {
		var input = e.target;
		if (!input || !input.closest) {
			return;
		}

		var form = input.closest('.wpcf7-form');
		if (form && (form.classList.contains('sent') || form.getAttribute('data-status') === 'sent')) {
			var responseEl = form.querySelector('.wpcf7-response-output');
			hideMessage(responseEl, form);
		}
	});

})();
