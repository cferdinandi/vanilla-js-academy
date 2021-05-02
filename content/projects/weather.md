---
title: "Weather App"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: false
---

<div id="app">Checking the weather near you...</div>

{{<js>}}
function getTheWeather (apiKey, options) {

	// Make sure an API key is provided
	if (!apiKey) throw 'An API key is required.';

	// Get app settings
	let {showIcon, message, units} = Object.assign({
		showIcon: true,
		message: 'It is currently {{temp}} degrees and {{conditions}} in {{city}}, {{state}}}.',
		units: 'I'
	}, options);

	// Get the #app element
	let app = document.querySelector('#app');

	/**
	 * Sanitize and encode all HTML in a user-submitted string
	 * https://portswigger.net/web-security/cross-site-scripting/preventing
	 * @param  {String} str  The user-submitted string
	 * @return {String} str  The sanitized string
	 */
	function sanitizeHTML (str) {
		return str.toString().replace(/javascript:/gi, '').replace(/[^\w-_. ]/gi, function (c) {
			return `&#${c.charCodeAt(0)};`;
		});
	}

	/**
	 * Get the weather description
	 * @param  {Object} weather The weather data
	 * @return {String}         The weather description
	 */
	function getDescription (weather) {
		return message.replace('{{temp}}', sanitizeHTML(weather.temp)).replace('{{conditions}}', sanitizeHTML(weather.weather.description)).replace('{{city}}', sanitizeHTML(weather.city_name)).replace('{{state}}', sanitizeHTML(weather.state_code));
	}

	/**
	 * Render the weather data into the DOM
	 * @param  {Object} weather The weather data object
	 */
	let renderWeather = function (weather) {
		console.log(weather);
		app.innerHTML =
			`${showIcon ? `<p>
				<img src="https://www.weatherbit.io/static/img/icons/${sanitizeHTML(weather.weather.icon)}.png">
			</p>` : ''}
			<p>${getDescription(weather)}</p>`;
	};

	// Get the user's location by IP address
	// Then, pass that into the weather API and get the current weather
	fetch('https://ipapi.co/json').then(function (response) {
		if (response.ok) {
			return response.json();
		}
		throw response;
	}).then(function (data) {

		// Pass data into another API request
		// Then, return the new Promise into the stream
		return fetch(`https://api.weatherbit.io/v2.0/current?key=${apiKey}&lat=${data.latitude}&lon=${data.longitude}&units=I`);

	}).then(function (response) {
		if (response.ok) {
			return response.json();
		}
		throw response;
	}).then(function (data) {

		// Pass the first weather item into a helper function to render the UI
		renderWeather(data.data[0]);

	}).catch(function (error) {

		// Show an error message
		app.textContent = 'Unable to get weather data at this time.';
		console.warn(error);

	});

}

getTheWeather('9dc2cfba9102410ca49f4bc3262a9413');
{{</js>}}