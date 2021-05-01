---
title: "Notebook App"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: false
---

{{<css>}}
textarea {
	margin-bottom: 1em;
	min-height: 50vh;
}
{{</css>}}

<form id="notebook">
	<label for="note">Write your notes in the form below. They'll save automatically as you type.</label>
	<textarea id="note" name="note"></textarea>

	<label for="date">Remind Me On</label>
	<input type="date" id="date" name="date">
</form>

{{<js>}}
// Get the notebook
let notebook = document.querySelector('#notebook');

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
 * Load note from localStorage
 */
function loadNote () {

	// Get saved data
	let saved = JSON.parse(localStorage.getItem('notes'));
	if (!saved) return;

	// Get form elements
	let fields = notebook.elements;

	// Loop through each one and load data
	for (let field of fields) {

		// If there's no saved data, skip it
		if (!saved[field.name]) continue;

		// Set the field value to the saved data in localStorage
		field.value = saved[field.name];

	}
}

/**
 * Save note to localStorage
 */
function saveNote () {
	let notes = serialize(new FormData(notebook));
	localStorage.setItem('notes', JSON.stringify(notes));
}

// Load saved note from localStorage
loadNote();

// Listen for changes
notebook.addEventListener('input', saveNote);
notebook.addEventListener('submit', function (event) {
	event.preventDefault();
});
{{</js>}}