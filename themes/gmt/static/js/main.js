/*! academy v1.1.0 | (c) 2020 Chris Ferdinandi | MIT License | http://github.com/cferdinandi/vanilla-js-academy */
(function () {
	'use strict';

	/**
	 * Element.matches() polyfill (simple version)
	 * https://developer.mozilla.org/en-US/docs/Web/API/Element/matches#Polyfill
	 */
	if (!Element.prototype.matches) {
		Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
	}

	// Variables
	var buyNow = document.querySelectorAll('.edd-buy-now-button');

	// Handle "buy now" clicks
	// Don't run if right-click or command/control + click
	var buyNowHandler = function (event) {
		if (!event.target.classList.contains('edd-buy-now-button')) return;
		if (event.button !== 0 || event.metaKey || event.ctrlKey) return;
		event.target.innerHTML = 'Adding to cart...';
		event.target.classList.add('disabled');
	};

	// Listen for "buy now" clicks
	if (buyNow.length > 0) {
		document.addEventListener('click', buyNowHandler, false);
	}

	/**
	 *
	 * @param {Function} callback
	 */
	var mailchimp = function (callback) {


		//
		// Variables
		//

		// Fields
		var form = document.querySelector('#mailchimp-form');
		if (!form) return;
		var email = form.querySelector('#mailchimp-email');
		if (!email) return;
		var status = form.querySelector('#mc-status');
		var btn = form.querySelector('[data-processing]');

		// Messages
		var messages = {
			empty: 'Please provide an email address.',
			notEmail: 'Please use a valid email address.',
			success: 'Success! Thanks for inviting me to your inbox.'
		};

		// Endpoint
		var endpoint = 'https://gomakethings.com/checkout/wp-json/gmt-mailchimp/v1/subscribe';


		//
		// Methods
		//

		/**
		 * Serialize the form data into a query string
		 * https://stackoverflow.com/a/30153391/1293256
		 * @param  {Node}   form The form to serialize
		 * @return {String}      The serialized form data
		 */
		var serialize = function (form) {

			// Setup our serialized data
			var serialized = [];

			// Loop through each field in the form
			for (var i = 0; i < form.elements.length; i++) {

				var field = form.elements[i];

				// Don't serialize fields without a name, submits, buttons, file and reset inputs, and disabled fields
				if (!field.name || field.disabled || field.type === 'file' || field.type === 'reset' || field.type === 'submit' || field.type === 'button') continue;

				// Convert field data to a query string
				if ((field.type !== 'checkbox' && field.type !== 'radio') || field.checked) {
					serialized.push(encodeURIComponent(field.name) + "=" + encodeURIComponent(field.value));
				}
			}

			return serialized.join('&');

		};

		var clearStatus = function () {

			// Bail if there's no status container
			if (!status) return;

			// Wipe classes and HTML from the status
			status.textContent = '';
			status.className = '';

			// Wipe classes and aria labels from the email field
			email.className = '';
			email.removeAttribute('aria-describedby');

		};

		var showStatus = function (msg, success) {

			// Bail if there's no status container
			if (!status) return;

			// Update the status message
			status.textContent = msg;

			// Set status class
			if (success) {
				status.className = 'success-message';
				status.setAttribute('tabindex', '-1');
				status.focus();
			} else {
				status.className = 'error-message';
				email.className = 'error';
				email.setAttribute('aria-describedby', 'mc-status');
				email.focus();
			}

		};

		var disableButton = function () {
			if (!btn) return;
			btn.setAttribute('data-original', btn.innerHTML);
			btn.innerHTML = btn.getAttribute('data-processing');
			btn.classList.add('disabled');
		};

		var enableButton = function () {
			if (!btn) return;
			btn.innerHTML = btn.getAttribute('data-original');
			btn.classList.remove('disabled');
		};

		var sendData = function (params) {

			// Set up our HTTP request
			var xhr = new XMLHttpRequest();

			// Setup our listener to process compeleted requests
			xhr.onreadystatechange = function () {

				// Only run if the request is complete
				if ( xhr.readyState !== 4 ) return;

				// Show status message
				var success = xhr.status === 200 ? true : false;
				var response = JSON.parse(xhr.responseText);
				if (success) {
					showStatus(messages.success, success);
				} else {
					showStatus(response.message, success);
				}

				// Reenable button
				enableButton();

				// If there's a callback, run it
				if (callback && typeof callback === 'function') {
					callback(response);
				}

			};

			// Create and send a GET request
			// The first argument is the post type (GET, POST, PUT, DELETE, etc.)
			// The second argument is the endpoint URL
			xhr.open('POST', endpoint + '?' + params);
			xhr.send();

		};

		// Submit the form
		var submitForm = function () {

			// If already submitting, don't submit again
			if (btn && btn.matches('.disabled')) return;

			// Disable the submit button
			disableButton();

			// Send the data to the MailChimp API
			sendData(serialize(form));

		};

		var isEmail = function () {
			return /^([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22))*\x40([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d))*(\.\w{2,})+$/.test(email.value);
		};

		var validate = function () {

			// If no email is provided
			if (email.value.length < 1) {
				showStatus(messages.empty);
				return false;
			}

			// If email is not valid
			if (!isEmail()) {
				showStatus(messages.notEmail);
				return false;
			}

			return true;

		};

		var submitHandler = function (event) {

			// Stop form from submitting
			event.preventDefault();

			// Clear the status
			clearStatus();

			// Validate email
			var valid = validate();

			if (valid) {
				submitForm();
			}

		};


		//
		// Event Listeners & Inits
		//

		form.addEventListener('submit', submitHandler, false);

	};

	/**
	 * Load pricing parity message
	 */
	var pricingParity = function (endpoint, template) {

		// Make sure endpoint and template exist
		if (!endpoint) return;

		// Render the pricing parity message
		var renderPricingParity = function (data) {

			// Make sure we have data to render
			if (!data || !template) return;

			// Convert data to JSON
			data = JSON.parse(data);

			// Make sure discount exists
			if (data.status === 'no_discount') return;

			// Get the nav
			var nav = document.querySelector('header');
			if (!nav) return;

			// Create container
			var pricing = document.createElement('div');
			pricing.id = 'pricing-parity';
			pricing.className = 'bg-muted padding-top-small padding-bottom-small';
			pricing.innerHTML = template.replace('{{iso}}', data.code).replace('{{country}}', data.country).replace('{{code}}', data.discount).replace('{{amount}}', data.amount);

			// Insert into the DOM
			nav.parentNode.insertBefore(pricing, nav);

		};

		// Get the pricing parity message via Ajax
		var getPricingParity = function () {

			// Set up our HTTP request
			var xhr = new XMLHttpRequest();
			if (!('responseType' in xhr)) return;

			// Setup our listener to process compeleted requests
			xhr.onreadystatechange = function () {
				// Only run if the request is complete
				if (xhr.readyState !== 4) return;

				// Process our return data
				if (xhr.status === 200) {

					// Save the content to sessionStorage
					sessionStorage.setItem('gmt-pricing-parity', xhr.response);

					// Render it
					renderPricingParity(xhr.response);

				}
			};

			// Create and send a GET request
			xhr.open('GET', endpoint);
			xhr.send();

		};

		// Get and render pricing parity info
		var pricing = sessionStorage.getItem('gmt-pricing-parity');
		if (typeof pricing === 'string') {
			renderPricingParity(pricing);
		} else {
			getPricingParity();
		}

	};

	// Mailchimp form
	if (document.querySelector('#mailchimp-form')) {
		mailchimp(function (data) {
			if (data.code === 200) {
				window.location.href = '/signup-success';
			}
		});
	}

	// Pricing parity
	pricingParity('https://gomakethings.com/checkout/wp-json/gmt-pricing-parity/v1/discount/', '<div class="container container-large"><img width="100" style="float:left;margin: 0.125em 1em 1em 0;" src="https://flagpedia.net/data/flags/normal/{{iso}}.png"><p class="text-small no-margin-bottom">Hi! Looks like you\'re from <strong>{{country}}</strong>, where the <strong>Vanilla JS Academy</strong> might be a bit expensive. A <strong>{{amount}}% discount</strong> will automatically be applied to the program at checkout. Cheers!</p></div>');

}());