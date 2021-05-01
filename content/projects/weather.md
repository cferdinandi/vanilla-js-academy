---
title: "Weather App"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: false
---

<div id="app">Checking the weather near you...</div>

{{<js>}}
let getTheWeather = (function () {

	'use strict';


	//
	// Variables
	//

	// Default settings
	let defaults = {
		apiKey: null,
		selector: '#app',
		convertTemp: true,
		description: 'It is currently {{temp}} degrees and {{conditions}} in {{city}}, {{state}}.',
		noWeather: 'Unable to get weather data at this time. Sorry!',
		showIcon: true
	};

	// An object to hold the public methods
	let methods = {};

	// Placeholder variables for the app element and settings object
	let app, settings;


	//
	// Methods
	//

	/**
	 * Sanitize and encode all HTML in a user-submitted string
	 * @param  {String} str  The user-submitted string
	 * @return {String} str  The sanitized string
	 */
	let sanitizeHTML = function (str) {
		let temp = document.createElement('div');
		temp.textContent = str;
		return temp.innerHTML;
	};

	/**
	 * Convert fahrenheit to celcius
	 * @param  {String} temp The temperature in celcius
	 * @return {Number}      The temperature in fahrenheit
	 */
	let fToC = function (temp) {

		// If temperature should be converted, convert it
		if (settings.convertTemp) {
			return (parseFloat(temp) * 9/5) + 32;
		}

		// Otherwise, return it as-is
		return temp;

	};

	/**
	 * Get the icon for the current weather conditions
	 * @param  {Object} weather The weather object
	 * @return {String}         A markup string for the icon
	 */
	let getIcon = function (weather) {

		// If the icon is deactivated, return an empty string
		if (!settings.showIcon) return '';

		// Otherwise, return the icon
		let html =
			'<p>' +
				'<img src="https://www.weatherbit.io/static/img/icons/' + weather.weather.icon + '.png">' +
			'</p>';
		return html;

	};

	/**
	 * Get the description for the current weather conditions
	 * @param  {Object} weather The weather object
	 * @return {String}         A markup string for the weather description
	 */
	let getDescription = function (weather) {
		return settings.description
			.replace('{{temp}}', fToC(sanitizeHTML(weather.temp)))
			.replace('{{conditions}}', sanitizeHTML(weather.weather.description).toLowerCase())
			.replace('{{city}}', sanitizeHTML(weather.city_name))
			.replace('{{state}}', sanitizeHTML(weather.state_code));
	};

	/**
	 * Render the weather data into the DOM
	 * @param  {Object} weather The weather data object
	 */
	let renderWeather = function (weather) {
		app.innerHTML =
			getIcon(weather) +
			'<p>' + getDescription(weather) + '</p>';
	};

	/**
	 * Display a message when no weather data can be found
	 */
	let renderNoWeather = function () {
		app.innerHTML = settings.noWeather;
	};

	/**
	 * Fetch location and weather data from APIs
	 */
	let fetchWeatherData = function () {

		// Get the user's location by IP address
		// Then, pass that into the weather API and get the current weather
		fetch('https://ipapi.co/json').then(function (response) {
			// Get the JSON object from the response
			return response.json();
		}).then(function (data) {
			// Pass data into another API request
			// Then, return the new Promise into the stream
			return fetch('https://api.weatherbit.io/v2.0/current?key=' + settings.apiKey + '&lat=' + data.latitude + '&lon=' + data.longitude);
		}).then(function (response) {
			// Get the JSON object from the weather API
			return response.json();
		}).then(function (data) {
			// Pass the first weather item into a helper function to render the UI
			renderWeather(data.data[0]);
		}).catch(function () {
			renderNoWeather();
		});

	};

	/**
	 * Get weather data for a specific city/state
	 * @param  {String} city The city and state
	 */
	methods.weatherByCity = function (city) {
		fetch('https://api.weatherbit.io/v2.0/current?key=' + settings.apiKey + '&city=' + encodeURIComponent(city)).then(function (response) {
			return response.json();
		}).then(function (data) {
			renderWeather(data.data[0]);
		}).catch(function () {
			renderNoWeather();
		});
	};

	/**
	 * Initialize the plugin
	 * @param  {Object} options User options
	 */
	methods.init = function (options) {

		// Merge user options into default settings
		settings = Object.assign(defaults, options);

		// Don't run if no API key was provided
		if (!settings.apiKey) {
			console.warn('Please provide an API key.');
			return;
		}

		// Get the #app element
		app = document.querySelector(settings.selector);

		// Fetch weather data
		fetchWeatherData();

	};


	//
	// Return public methods
	//

	return methods;


})();

// Initialize the plugin
getTheWeather.init({
	apiKey: '9dc2cfba9102410ca49f4bc3262a9413'
});
{{</js>}}