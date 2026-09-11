/**
 * Instagram Theme Settings & Diagnostics Admin JS.
 *
 * @package Custom_Theme
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		var $wrap      = $('#sarathi-insta-diagnostic-wrap');
		if (!$wrap.length) return;

		var $btnTest   = $('#sarathi-btn-test-connection');
		var $btnSync   = $('#sarathi-btn-sync-now');
		var $spinner   = $('#sarathi-insta-action-spinner');
		var $feedback  = $('#sarathi-insta-ajax-feedback');
		var $badge     = $('#sarathi-insta-status-badge');
		var $lastSync  = $('#sarathi-insta-last-sync');
		var $cacheCnt  = $('#sarathi-insta-cached-count');
		var $errNotice = $('#sarathi-insta-error-notice');

		function getFieldVal(fieldKey) {
			var $input = $('[name*="' + fieldKey + '"], [id*="' + fieldKey + '"]');
			return $input.length ? $input.val() : '';
		}

		function setBusy(isBusy, message) {
			if (isBusy) {
				$spinner.addClass('is-active');
				$btnTest.prop('disabled', true);
				$btnSync.prop('disabled', true);
				$feedback.removeClass('is-success is-error').text(message || '');
			} else {
				$spinner.removeClass('is-active');
				$btnTest.prop('disabled', false);
				$btnSync.prop('disabled', false);
			}
		}

		function updateBadge(badgeClass, labelText) {
			$badge.removeClass('status-connected status-expired status-error status-neutral')
			      .addClass(badgeClass);
			$badge.find('.sarathi-insta-status-text').text(labelText);
		}

		// -------------------------------------------------------------
		// Test Connection Handler
		// -------------------------------------------------------------
		$btnTest.on('click', function(e) {
			e.preventDefault();

			var accountId   = getFieldVal('field_insta_account_id');
			var accessToken = getFieldVal('field_insta_access_token');

			setBusy(true, sarathiInstagramAdmin.strings.testing);

			$.ajax({
				url: sarathiInstagramAdmin.ajaxUrl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'custom_theme_instagram_test_connection',
					nonce: sarathiInstagramAdmin.nonce,
					account_id: accountId,
					access_token: accessToken
				}
			}).done(function(res) {
				setBusy(false);

				if (res.success && res.data) {
					updateBadge(res.data.status_class, res.data.status_label);
					$feedback.addClass('is-success').text(res.data.message);

					// If error notice was visible, hide it
					if ($errNotice.length) {
						$errNotice.slideUp(200);
					}

					// Update avatar / username if provided
					if (res.data.username) {
						$('.sarathi-insta-diag-title').text('@' + res.data.username);
					}
					if (res.data.avatar) {
						var $avatar = $('.sarathi-insta-diag-avatar');
						if ($avatar.length) {
							$avatar.attr('src', res.data.avatar);
						}
					}
				} else {
					var err = (res.data && res.data.message) ? res.data.message : sarathiInstagramAdmin.strings.error;
					var statusClass = (res.data && res.data.status_class) ? res.data.status_class : 'status-error';
					var statusLabel = (res.data && res.data.status_label) ? res.data.status_label : 'Error';

					updateBadge(statusClass, statusLabel);
					$feedback.addClass('is-error').text(err);

					if ($errNotice.length) {
						$errNotice.find('.sarathi-insta-error-text').text(err);
						$errNotice.slideDown(200);
					}
				}
			}).fail(function(xhr, status, error) {
				setBusy(false);
				updateBadge('status-error', 'Server Error');
				$feedback.addClass('is-error').text(sarathiInstagramAdmin.strings.error + ' (' + error + ')');
			});
		});

		// -------------------------------------------------------------
		// Sync Posts Now Handler
		// -------------------------------------------------------------
		$btnSync.on('click', function(e) {
			e.preventDefault();

			setBusy(true, sarathiInstagramAdmin.strings.syncing);

			$.ajax({
				url: sarathiInstagramAdmin.ajaxUrl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'custom_theme_instagram_sync_now',
					nonce: sarathiInstagramAdmin.nonce
				}
			}).done(function(res) {
				setBusy(false);

				if (res.success && res.data) {
					updateBadge(res.data.status_class, res.data.status_label);
					$feedback.addClass('is-success').text(res.data.message);

					if ($lastSync.length && res.data.last_sync) {
						$lastSync.text(res.data.last_sync);
					}
					if ($cacheCnt.length && res.data.total !== undefined) {
						$cacheCnt.text(res.data.total);
					}

					if ($errNotice.length) {
						$errNotice.slideUp(200);
					}
				} else {
					var err = (res.data && res.data.message) ? res.data.message : sarathiInstagramAdmin.strings.error;
					var statusClass = (res.data && res.data.status_class) ? res.data.status_class : 'status-error';
					var statusLabel = (res.data && res.data.status_label) ? res.data.status_label : 'Sync Error';

					updateBadge(statusClass, statusLabel);
					$feedback.addClass('is-error').text(err);

					if ($errNotice.length) {
						$errNotice.find('.sarathi-insta-error-text').text(err);
						$errNotice.slideDown(200);
					}
				}
			}).fail(function(xhr, status, error) {
				setBusy(false);
				updateBadge('status-error', 'Server Error');
				$feedback.addClass('is-error').text(sarathiInstagramAdmin.strings.error + ' (' + error + ')');
			});
		});
	});

})(jQuery);
