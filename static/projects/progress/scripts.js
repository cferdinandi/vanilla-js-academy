;(function (window, document, undefined) {

	'use strict';

	//
	// Variables
	//

	var fields = document.querySelectorAll('.field-save');
	var review = document.querySelector('#review');
	var progress = document.querySelector('.progress');

	//
	// Methods
	//

	/**
	 * Sanitize and encode all HTML in a user-submitted string
	 * @param  {String} str  The user-submitted string
	 * @return {String} str  The sanitized string
	 */
	var sanitizeHTML = function (str) {
		var temp = document.createElement('div');
		temp.textContent = str;
		return temp.innerHTML;
	};

	/**
	 * Get the progress as a percentage
	 * @param  {Node}   progress The progress bar element
	 * @param  {Object} saved    The saved data object
	 * @return {String}          The percentage completed
	 */
	var getProgress = function (progress, saved) {

		// Get the fields to check from the DOM
		var sections = progress.getAttribute('data-progress');
		if (!sections) return '0';

		// Replace single quotes with doubles and convert to an array with JSON.parse()
		sections = JSON.parse(sections.replace(new RegExp("'", 'g'), '"'));

		// Calculate how many sections are completed
		var completed = 0;

		// Loop through each section
		sections.forEach(function (section) {

			console.log(section);

			// Mark completed by default
			var isComplete = true;

			// Loop through each required field in the section
			section.forEach(function (field) {

				// If the field isn't saved or is empty, mark section incomplete
				if (!saved[field] || saved[field].length < 1) {
					isComplete = false;
				}

			});

			// If the section is complete, update the total
			if (isComplete) {
				completed++;
			}

		});

		// Get a percentage by dividing number of completed sections by total
		return (completed / sections.length * 100).toString() + '%';

	};

	/**
	 * Show the progress bar field
	 */
	var showProgress = function () {

		// If there's no progress bar, bail
		if (!progress) return;

		// Get saved fields
		var saved = localStorage.getItem('progressBar');
		if (!saved) return;
		saved = JSON.parse(saved);

		// Create the progress bar
		var bar = document.createElement('div');
		bar.className = 'progress-bar';
		bar.style.width = getProgress(progress, saved);

		// Inject the progress bar into the DOM
		progress.append(bar);

	};

	/**
	 * Save fields
	 */
	var saveFields = function () {

		// If there are no fields in the DOM, bail
		if (fields.length < 1) return;

		// Check if there's any data already in localStorage
		var saved = localStorage.getItem('progressBar');

		// If there is, use it
		// Otherwise, create a new object
		var data = saved ? JSON.parse(saved) : {};

		// Loop through each field and push its value into our data object
		fields.forEach(function (field) {
			if (!field.name) return;
			data[field.name] = field.value;
		});

		// Stringify the data and save it to localStorage
		localStorage.setItem('progressBar', JSON.stringify(data));

	};

	/**
	 * Load saved fields into the DOM
	 */
	var loadFields = function () {

		// If there are no fields in the DOM, bail
		if (fields.length < 1) return;

		// Check if there's data saved in localStorage
		// If not, bail
		// Otherwise, convert it to an object
		var saved = localStorage.getItem('progressBar');
		if (!saved) return;
		saved = JSON.parse(saved);

		// Loop through each field and set its value to whats saved in localStorage
		fields.forEach(function (field) {
			if (!field.name) return;
			if (!saved[field.name]) return;
			field.value = saved[field.name];
		});

	};

	/**
	 * Create the markup for the review page
	 * @param  {String} saved Our saved data
	 * @return {String}       The markup
	 */
	var createReviewMarkup = function (saved) {

		// Convert the data from a string to an object
		saved = JSON.parse(saved);

		// Setup a markup placeholder
		var markup = '';

		// Loop through each item and generate its markup
		Object.keys(saved).forEach(function (field) {
			markup += '<li><strong>' + field.toUpperCase() + ':</strong> ' + sanitizeHTML(saved[field]) + '</li>';
		});

		// Return the markup
		return '<ul>' + markup + '</ul>';

	};

	/**
	 * Show a summary for review
	 */
	var showSummary = function () {

		// If there's no review element, bail
		if (!review) return;

		// Get content from localStorage
		var saved = localStorage.getItem('progressBar');

		// If there's no data, show an error message
		if (!saved) {
			review.innerHTML = 'You have not filled in any fields yet.';
			return;
		}

		// Generate the markup and inject it into the DOM
		review.innerHTML = createReviewMarkup(saved);

	};


	//
	// Inits & Event Listeners
	//

	// Load content
	loadFields();
	showProgress();
	showSummary();

	// Listen for clicks
	document.addEventListener('click', function (event) {
		if (!event.target.matches('.btn-save')) return;
		saveFields();
	}, false);

})(window, document);