---
title: "Form Saver"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: true
---

<form id="save-me">

	<label for="name">Name</label>
	<input type="text" name="name" id="name">

	<label for="address">Address</label>
	<input type="text" name="address" id="address">

	<label for="email">Email</label>
	<input type="email" name="email" id="email">

	<label for="more">Additional thoughts?</label>
	<textarea name="more" id="more"></textarea>

	<div class="callout">
		<p>Not ready to submit? Save your answers for later.</p>
		<button class="btn btn-secondary" type="button" data-form="save">Save Answers</button>
		<button class="btn btn-tertiary" type="button" data-form="clear">Clear</button>
	</div>

	<p>
		<button class="btn" type="submit">Submit</button>
	</p>

</form>

{{<js>}}
// Get the form element
let form = document.querySelector('#save-me');

// localStorage prefix
let prefix = 'autosave_fields';

// Hold current notification
let currentNotification;

/**
 * Serialize all form data into an object
 * @param  {FormData} data The FormData object to serialize
 * @return {String}        The serialized form data
 */
function serialize (data) {
	let obj = {};
	for (let [key, value] of data) {
		if (obj[key] !== undefined) {
			if (!Array.isArray(obj[key])) {
				obj[key] = [obj[key]];
			}
			obj[key].push(value);
		} else {
			obj[key] = value;
		}
	}
	return obj;
}

/**
 * Show a "fields saved" status message
 * @param {Boolean} cleared If true, saved data was erased
 */
function showStatus (cleared) {

	// Create a notification
	let notification = document.createElement('div');
	notification.setAttribute('aria-live', 'polite');

	// Inject it into the DOM
	form.append(notification);

	// Add text after it's in the UI
	// Remove the current notification
	setTimeout(function () {

		// Show the status message
		notification.textContent = cleared ? 'Your info has been deleted.' : 'Your info has been saved. You can complete and submit the form later.';

		// If there's a current notification, remove it
		if (currentNotification) {
			currentNotification.remove();
		}

		// Store the new notification
		currentNotification = notification;

	}, 1);

	// Clear it after 6 seconds
	setTimeout(function () {
		notification.remove();
	}, 6000);

}

/**
 * Clear all of the saved fields from storage
 */
function clearStorage () {
	localStorage.removeItem(prefix);
}

/**
 * Save form data
 */
function saveFormData () {

	// Serialize the form fields
	let data = serialize(new FormData(form));

	// Stringify the object and save it to localStorage
	localStorage.setItem(prefix, JSON.stringify(data));

	// Show a "data saved" message
	showStatus();

}

/**
 * Handle click events
 * @param  {Event} event The event object
 */
function clickHandler (event) {

	// Only run on [data-form] buttons
	let type = event.target.getAttribute('data-form');
	if (!type) return;

	// If button type is "save", save form data
	if (type === 'save') {
		saveFormData();
	}

	// If button type is "clear", delete form data
	if (type === 'clear') {
		clearStorage();
		showStatus(true);
		form.reset();
	}

}

/**
 * Load saved data from localStorage
 */
function loadSaved () {

	// Get saved data from localStorage
	// If there's nothing saved, bail
	let saved = JSON.parse(localStorage.getItem(prefix));
	if (!saved) return;

	// Get all of the fields in the form
	let fields = form.elements;

	// Loop through each one and load saved data if it exists
	for (let field of fields) {
		if (!saved[field.name]) continue;
		field.value = saved[field.name];
	}

}

// Load saved data from localStorage
loadSaved();

// Listen for DOM events
document.addEventListener('click', clickHandler);
form.addEventListener('submit', clearStorage);
{{</js>}}