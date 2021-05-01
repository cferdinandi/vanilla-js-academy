---
title: "Dragon Trainer Monthly"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: false
---

An API-driven magazine for people who train dragons.

<div id="app"></div>

{{<js>}}
// Get the app element
let app = document.querySelector('#app');

/**
 * Sanitize and encode all HTML in a user-submitted string
 * https://portswigger.net/web-security/cross-site-scripting/preventing
 * @param  {String} str  The user-submitted string
 * @return {String} str  The sanitized string
 */
function sanitizeHTML (str) {
	return str.replace(/javascript:/gi, '').replace(/[^\w-_. ]/gi, function (c) {
		return `&#${c.charCodeAt(0)};`;
	});
}

/**
 * Find the first matching author
 * @param  {String} name    The author name
 * @param  {Array}  authors The author details
 * @return {Array}          The author
 */
function getAuthor (name, authors) {
	return authors.find(function (author) {
		return author.author === name;
	});
}

/**
 * Render articles into the DOM
 * @param  {Array} articles The articles to render
 * @param  {Array} authors  The author details
 */
function render (articles, authors) {

	// Create a new array of markup strings with array.map(), then
	// Combine them into one string with array.join(), then
	// Insert them into the DOM with innerHTML
	app.innerHTML = articles.map(function (article) {
		let author = getAuthor(article.author, authors);
		return `
			<article>
				<h2><a href="${sanitizeHTML(article.url)}">${sanitizeHTML(article.title)}</a></h2>
				<p><em>By ${sanitizeHTML(author ? `${author.author} - ${author.bio}` : article.author)}</em></p>
				<p>${sanitizeHTML(article.article)}</p>
			</article>`;
	}).join('');

}

// Get articles
Promise.all([
	fetch('https://vanillajsacademy.com/api/dragons.json'),
	fetch('https://vanillajsacademy.com/api/dragons-authors.json'),
]).then(function (responses) {
	return Promise.all(responses.map(function (response) {
		return response.json();
	}));
}).then(function (data) {

	// Render them into the DOM
	render(data[0].articles, data[1].authors);

}).catch(function (error) {
	console.warn(error);
});
{{</js>}}