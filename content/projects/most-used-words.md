---
title: "Most Used Words"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: true
---

<label for="text">Enter your text below.</label>
<textarea id="text"></textarea>

<p id="count" aria-live="polite">You've written 0 words and 0 characters.</p>

{{<js>}}
// Get the text area, and word and character count elements
let text = document.querySelector('#text');
let count = document.querySelector('#count');

/**
 * Get the most used words
 * @param  {Array} words The words entered into the textarea
 * @return {Object}      The most used words and their frequency
 */
function getMostUsedWord (words) {

	// Track the frequency of each word
	let frequency = {};

	// Loop through each word and count it's occurrences
	for (let word of words) {

		// Convert to lowercase
		word = word.toLowerCase();

		// If word isn't tracked yet, add it
		if (!frequency[word]) {
			frequency[word] = 0;
		}

		// Increase count by 1
		frequency[word]++;

	}

	// Sort by frequency
	// Filter out all but the most used words
	let mostUsed = Object.entries(frequency).sort(function (word1, word2) {
		if (word1[1] > word2[1]) {
			return -1;
		} else {
			return 1;
		}
	}).filter(function (word, index, sorted) {
		return word[1] === sorted[0][1];
	});

	return {
		words: mostUsed.map(function (word) {
			return `"${word[0]}"`;
		}),
		frequency: mostUsed.length ? mostUsed[0][1] : 0
	};

}

// Listen for changes to the text area content
text.addEventListener('input', function () {

	// Get the word count
	let words = text.value.split(/[\s]+/g).filter(function (word) {
		return word.length;
	});

	// Get the most used words
	let mostUsed = getMostUsedWord(words);
	console.log(mostUsed);

	// Display the current counts
	count.textContent = `You've written ${words.length} words and ${text.value.length} characters. ${mostUsed.frequency > 1 ? `Your most used words are ${mostUsed.words.join(' and ')}, which you used ${mostUsed.frequency} times.` : ''}`;

});
{{</js>}}