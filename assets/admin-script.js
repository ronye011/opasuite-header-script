(function($) {
	'use strict';

	$(document).ready(function() {

		// Tab Switching
		$('.opasuite-tab-btn').on('click', function(e) {
			e.preventDefault();
			var targetTab = $(this).data('tab');

			$('.opasuite-tab-btn').removeClass('active');
			$('.opasuite-tab-content').removeClass('active');

			$(this).addClass('active');
			$('#' + targetTab).addClass('active');

			if (targetTab === 'tab-preview') {
				updatePreviewCode();
			}
		});

		// Search / Filter Pages List
		$('#opasuite-search-pages').on('keyup input', function() {
			var query = $(this).val().toLowerCase();
			$('.opasuite-checkbox-list li').each(function() {
				var text = $(this).text().toLowerCase();
				if (text.indexOf(query) !== -1) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		});

		// Select All
		$('#opasuite-select-all').on('click', function() {
			$('.opasuite-checkbox-list li:visible input[type="checkbox"]').prop('checked', true);
		});

		// Deselect All
		$('#opasuite-deselect-all').on('click', function() {
			$('.opasuite-checkbox-list li:visible input[type="checkbox"]').prop('checked', false);
		});

		// Dynamic Preview Update
		function updatePreviewCode() {
			var domain = $('#opasuite_domain').val() || 'https://lowcode.opasuite.com.br';
			var token = $('#opasuite_token').val() || '69c27ed98f4ad77c46cd4634';
			var permitirAnonimo = $('#opasuite_permitir_login_anonimo').val() || 'on';
			var facebookAppId = $('#opasuite_facebook_appid').val() || '';
			var googleCred = $('#opasuite_google_credential').val() || '';
			var googleOauth = $('#opasuite_google_oauth').val() || '';

			var configObj = {
				"permitir_login_anonimo": permitirAnonimo,
				"facebook_appid": facebookAppId,
				"google_credential": googleCred,
				"google_oauth": googleOauth
			};

			var jsonStr = JSON.stringify(configObj, null, 2);

			var scriptCode = '(function(\n' +
'  i, s, g, r, j, y, b, p, t, z, a\n' +
') {\n' +
'  a = s.createElement(r);\n' +
'  a.async = true;\n' +
'  a.src = g.concat(\n' +
'    b, j,\n' +
'    b, y,\n' +
'    p, j\n' +
'  );\n' +
'  s.head.appendChild(a);\n' +
'  a.onload = function() {\n' +
'    opa.init(g, t, z);\n' +
'  };\n' +
'})(\n' +
'  window,\n' +
'  document,\n' +
'  \'' + domain + '\',\n' +
'  \'script\',\n' +
'  \'js\',\n' +
'  \'opa\',\n' +
'  \'/\',\n' +
'  \'.\',\n' +
'  \'' + token + '\',\n' +
'  `' + jsonStr + '`\n' +
');';

			$('#opasuite-preview-output').text(scriptCode);
		}

		// Bind inputs to live update
		$('#opasuite_domain, #opasuite_token, #opasuite_permitir_login_anonimo, #opasuite_facebook_appid, #opasuite_google_credential, #opasuite_google_oauth').on('input change', function() {
			updatePreviewCode();
		});

		// Initial preview render
		updatePreviewCode();

		// Copy code button
		$('#opasuite-copy-code').on('click', function() {
			var codeText = $('#opasuite-preview-output').text();
			navigator.clipboard.writeText(codeText).then(function() {
				var $btn = $('#opasuite-copy-code');
				var origHtml = $btn.html();
				$btn.html('<span class="dashicons dashicons-yes"></span> Copiado!').css('color', '#4ade80');
				setTimeout(function() {
					$btn.html(origHtml).css('color', '');
				}, 2000);
			});
		});

	});

})(jQuery);
