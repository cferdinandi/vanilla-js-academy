---
title: "Form Saver"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: false
---

Your details will automatically save as you type. Reload the page to test it out.

(_The saved data is deleted when you submit._)

<form id="save-me">

	<label for="name">Name</label>
	<input type="text" name="name" id="name">

	<label for="address">Address</label>
	<input type="text" name="address" id="address">

	<label for="email">Email</label>
	<input type="email" name="email" id="email">

	<label for="more">Additional thoughts?</label>
	<textarea name="more" id="more"></textarea>

	<p>
		<button class="btn" type="submit">Submit</button>
	</p>

</form>

{{<js>}}
// Get the form element
let form = document.querySelector('#save-me');

// localStorage prefix
let prefix = 'autosave_fields';

// Hold debouncer
let debounce;

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
 */
function showStatus () {

	// Create a notification
	let notification = document.createElement('div');
	notification.setAttribute('aria-live', 'polite');

	// Inject it into the DOM
	form.append(notification);

	// Add text after it's in the UI
	setTimeout(function () {
		notification.textContent = 'Your info has been automatically saved. You can complete and submit the form later.';
	}, 1);

	// Clear it after 3 seconds
	setTimeout(function () {
		notification.remove();
	}, 6000);

}

/**
 * Save a field to localStorage
 * @param  {Node} field The field
 */
function saveFields () {

	// Clear any existing debounced functions
	if (debounce) {
		clearTimeout(debounce);
	}

	// Debounce saving the field
	debounce = setTimeout(function () {

		// Serialize the form fields
		let data = serialize(new FormData(form));

		// Stringify the object and save it to localStorage
		localStorage.setItem(prefix, JSON.stringify(data));

		// Show a status notification
		showStatus();

	}, 1000);

}

/**
 * Handle input events
 * @param  {Event} event The event object
 */
function inputHandler (event) {

	// Only run on fields inside the #save-me form
	if (!event.target.closest('#save-me')) return;

	// Save the field to localStorage
	saveFields();

}

/**
 * Clear all of the saved fields from storage
 */
function clearStorage () {
	localStorage.removeItem(prefix);
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
document.addEventListener('input', inputHandler);
form.addEventListener('submit', clearStorage);
{{</js>}}