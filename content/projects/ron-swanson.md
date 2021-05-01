---
title: "Random Ron"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: false
---

<p><img style="width: 100%;" alt="Ron Swanson standing in front of a buffet stating, 'I've said too much.'" src="https://giphygifs.s3.amazonaws.com/media/iofbYa67AbBg4/giphy.gif"></p>

<blockquote id="quote" aria-live="polite">
<em>Getting a fresh quote...</em>
</blockquote>

<p>
<button class="btn" id="get-quote">More Ron</button>
</p>

{{<js>}}
// Get the blockquote and button elements
let quote = document.querySelector('#quote');
let btn = document.querySelector('#get-quote');

// Hold previous used quotes
let quotes = [];

// Get a fresh quote and render it into the DOM
function getQuote () {
	fetch('https://ron-swanson-quotes.herokuapp.com/v2/quotes').then(function (response) {
		if (response.ok) {
			return response.json();
		}
		throw response.status;
	}).then(function (data) {

		// If there are at least 50 previously used quotes, remove the first one
		if (quotes.length > 49) {
			quotes.shift();
		}

		// If this quote was already used, recursively fetch a new one
		// Then, end the current callback function
		if (quotes.includes(data[0])) {
			getQuote();
			return;
		}

		// Otherwise, update the UI and add the quote to the list
		quote.textContent = data[0];
		quotes.push(data[0]);

	}).catch(function (error) {
		quote.textContent = '[Something went wrong, sorry!] I have a joke for you... The government in this town is excellent, and uses your tax dollars efficiently.';
	});
}

// Get a quote on page load
getQuote();

// Get a quote when the #get-quote button is clicked
btn.addEventListener('click', getQuote);
{{</js>}}