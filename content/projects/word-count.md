---
title: "Character and Word Count"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: false
---

<label for="text">Enter your text below.</label>
<textarea id="text"></textarea>

<div class="screen-reader" id="sr-count" aria-live="polite">0 words, 0 characters</div>
<p aria-hidden="true">You've written <strong><span id="word-count">0</span> words</strong> and <strong><span id="character-count">0</span> characters</strong>.</p>

{{<js>}}
// Get the text area, and word and character count elements
let text = document.querySelector('#text');
let wordCount = document.querySelector('#word-count');
let charCount = document.querySelector('#character-count');
let srCount = document.querySelector('#sr-count');

// Listen for changes to the text area content
text.addEventListener('input', function () {

	// Get the word count
	let words = text.value.split(/[\s]+/g).filter(function (word) {
		return word.length;
	});

	// Display the word count
	wordCount.textContent = words.length;

	// Display the characters count
	charCount.textContent = text.value.length;

	// Update announcement for screen readers
	srCount.textContent = `${words.length} words, ${text.value.length} characters`;

});
{{</js>}}