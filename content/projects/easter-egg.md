---
title: "Easter Egg"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: true
---

Enter the Konami Code on your keyboard: up, up, down, down, left, right, left, right, B, A.

{{<js>}}
/**
 * Run a key-based easter egg
 * @param  {Array}    pattern  The pattern to match
 * @param  {Function} callback The callback to run if a match happens
 */
function easterEgg (pattern, callback) {

	// Make sure both parameters are provided and a valid format
	if (!pattern || !Array.isArray(pattern) || !callback || typeof callback !== 'function') {
		throw 'Valid pattern and callback function required';
	}

	// The number of current matches
	let matches = 0;

	/**
	 * Handle key press events
	 * @param  {Event} event The event object
	 */
	function keyHandler (event) {

		// Check if the pressed key matches the current item in the pattern
		// If not, reset the matches
		if (event.code !== pattern[matches]) {
			matches = 0;
			return;
		}

		// If it does, increase the number of matches
		matches++;

		// If the full pattern is matched, give the user a prize!
		if (matches === pattern.length) {
			callback();
		}

	}

	// Listen for key events
	document.addEventListener('keydown', keyHandler);

}

// The pattern to match
let pattern = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'KeyB', 'KeyA'];

// Rickroll the user
function rickroll () {
	window.location.href = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
}

// Run the easter egg
easterEgg(pattern, rickroll);
{{</js>}}