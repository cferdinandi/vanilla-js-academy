---
title: "Timestamp"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: false
---

The date one year, two months, three weeks and four days from now is: <br><strong id="app"></strong>.

This is powered by a library you'll create that works like this:

```js
let now = new Stamp();
let future = now.addYears().addMonths(2).addWeeks(3).addDays(4).getDateString();
```

{{<js>}}
let Stamp = (function () {

	// Times in milliseconds
	let times = {
		day: 1000 * 60 * 60 * 24,
		week: 1000 * 60 * 60 * 24 * 7,
		month: 1000 * 60 * 60 * 24 * 30,
		year: 1000 * 60 * 60 * 24 * 365
	};

	// Default settings
	let defaults = {
		useSeconds: false
	};

	/**
	 * The constructor object
	 * @param {Integer|String|Date} date    The date to use for this timestamp
	 * @param {Object}              options Options and settings
	 */
	function Constructor (date, options = {}) {

		// Create settings
		let settings = Object.assign({}, defaults, options);
		Object.freeze(settings);

		// If useSeconds, convert date to milliseconds
		if (settings.useSettings && typeof date === 'number') {
			date = date * 1000;
		}

		// Define properties
		Object.defineProperties(this, {
			_settings: {value: settings},
			_timestamp: {value: date ? new Date(date).getTime() : new Date().getTime()}
		});

	}

	/**
	 * Add days to a timestamp
	 * @param  {Number}  n The number of days to add
	 * @return {Integer}   The new timestamp
	 */
	Constructor.prototype.addDays = function (n = 1) {
		return new Constructor(this._timestamp + (n * times.day));
	};

	/**
	 * Add weeks to a timestamp
	 * @param  {Number}  n The number of weeks to add
	 * @return {Integer}   The new timestamp
	 */
	Constructor.prototype.addWeeks = function (n = 1) {
		return new Constructor(this._timestamp + (n * times.week));
	};

	/**
	 * Add months to a timestamp
	 * @param  {Number}  n The number of months to add
	 * @return {Integer}   The new timestamp
	 */
	Constructor.prototype.addMonths = function (n = 1) {
		return new Constructor(this._timestamp + (n * times.month));
	};

	/**
	 * Add years to a timestamp
	 * @param  {Number}  n The number of years to add
	 * @return {Integer}   The new timestamp
	 */
	Constructor.prototype.addYears = function (n = 1) {
		return new Constructor(this._timestamp + (n * times.year));
	};

	/**
	 * Get the date object for a timestamp
	 * @return {Date} The date object
	 */
	Constructor.prototype.getDate = function () {
		return new Date(this._timestamp);
	};

	/**
	 * Get the date as a string for a timestamp
	 * @return {String} The date string
	 */
	Constructor.prototype.getDateString = function () {
		return this.getDate().toString();
	};

	/**
	 * Get the timestamp
	 * @return {Integer} The timestamp
	 */
	Constructor.prototype.timestamp = function () {
		return this._settings.useSeconds ? parseInt(this._timestamp / 1000, 10) : this._timestamp;
	};

	return Constructor;

})();

let app = document.querySelector('#app');
let now = new Stamp();
app.textContent = `${now.addYears().addMonths(2).addWeeks(3).addDays(4).getDateString()}`
{{</js>}}