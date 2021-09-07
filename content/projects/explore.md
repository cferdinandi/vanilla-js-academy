---
title: "Explore"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: false
---

{{<css>}}
/**
 * Layouts
 */

@media (min-width: 40em) {
	.place {
		display: grid;
		grid-template-columns: 1fr 2fr;
		grid-template-rows: 1fr;
		grid-column-gap: 2em;
	}
}

.place {
	margin-bottom: 2em;
}


.place h2 {
	margin: 0 0 0.5em;

	padding: 0;
}


/**
 * Form fields
 */

label {
	font-weight: bold;
}

select {
	margin-bottom: 1.5em;
}

label,
select {
	display: inline-block;
	margin-bottom: 1.5em;
	width: auto;
}


/**
 * Favorites
 */

button {
	background-color: #f7f7f7;
	border: 1px solid #e5e5e5;
	color: #272727;
	padding: 0 0.5em;
}

button[aria-pressed="true"] {
	background-color: #de391f;
	border-color: #de391f;
	color: #ffffff;
}

/**
 * Visually hide an element, but leave it available for screen readers
 * @link https://github.com/h5bp/html5-boilerplate/blob/master/dist/css/main.css
 * @link http://snook.ca/archives/html_and_css/hiding-content-for-accessibility
 * @link https://github.com/h5bp/main.css/issues/12#issuecomment-321106995
 */
.visually-hidden {
	border: 0;
	clip: rect(0 0 0 0);
	height: 1px;
	overflow: hidden;
	padding: 0;
	position: absolute;
	white-space: nowrap;
	width: 1px;
}
{{</css>}}

<p>Explore cool, quirky places in your own backyard.</p>

<div>
	<label for="filters">Filter Places:</label>
	<select id="filters">
		<option value="all" selected>All Places</option>
		<option value="faves">Favorites</option>
	</select>
</div>

<div id="app">Loading...</div>

<script src="https://cdn.jsdelivr.net/npm/reefjs@10/dist/reef.js"></script>

{{<js>}}
// Faves ID in localStorage
let favesID = 'places_faves';

// Get the view filters
let filters = document.querySelector('#filters');

/**
 * Get place data from the API and update the app state
 */
function getPlaces () {
	fetch('https://vanillajsacademy.com/api/places.json').then(function (response) {
		if (response.ok) {
			return response.json();
		}
		return Promise.reject(response);
	}).then(function (data) {
		app.data.places = data;
		app.data.faves = getFaves();
		app.data.view = 'all';
	}).catch(function (err) {
		console.warn(err);
		app.data.places = null;
	});
}

/**
 * Get favorite places from localStorage
 * @return {Object} Favorite places
 */
function getFaves () {
	let saved = JSON.parse(localStorage.getItem(favesID));
	return saved ? saved : {};
}

/**
 * The message to display if there's no places data
 * @return {String} The HTML
 */
function noPlacesHTML () {
	return '<p><em>Unable to find any places right now. Please try again later. Sorry!</em></p>';
}

/**
 * Create HTML for each of the places from the app data
 * @param  {Object} props The app data
 * @return {String}       The HTML
 */
function placesHTML (props) {

	// If view is favorites and there are none, show a message
	if (props.view === 'faves' && !Object.values(props.faves).find(function (fave) {
		return fave;
	})) {
		return `<p>You have no favorites yet.</p>`;
	}

	return props.places.map(function (place) {

		// If view is favorites and this isn't a favorite place, don't show it
		if (props.view === 'faves' && !props.faves[place.id]) return '';

		// Otherwise, get the HTML
		return `
			<div class="place">
				<div>
					<img alt="${place.imgAlt}" src="${place.img}">
				</div>
				<div>
					<h2>${place.place}</h2>
					<p>${place.description}</p>
					<p><em>${place.location}</em><br><a href="${place.url}">${place.url}</a></p>
					<p>
						<button data-fave="${place.id}" aria-pressed="${props.faves[place.id]}">
							<span aria-hidden="true">♥</span>
							<span class="visually-hidden">Save ${place.place}</span>
						</button>
					</p>
				</div>
			</div>`;

	}).join('');

}

/**
 * Handle click events
 * @param  {Event} event The event object
 */
function clickHandler (event) {

	// Only run on fave buttons
	let place = event.target.closest('[data-fave]');
	if (!place) return;

	// Get the place ID
	let placeID = place.getAttribute('data-fave');

	// If place is already faved, remove it
	// Otherwise, fave it
	app.data.faves[placeID] = app.data.faves[placeID] ? false : true;

	// Save faves to localStorage
	localStorage.setItem(favesID, JSON.stringify(app.data.faves));

}

/**
 * Update the view
 */
function changeView () {
	app.data.view = filters.value;
}

/**
 * The app component
 */
var app = new Reef('#app', {
	data: {},
	template: function (props) {

		// If there are places, render them
		if (props.places && props.places.length) {
			return placesHTML(props);
		}

		// Otherwise, show an error
		return noPlacesHTML();

	}
});

// Get places from the API
getPlaces();

// Event listeners
document.addEventListener('click', clickHandler);
filters.addEventListener('input', changeView);
{{</js>}}