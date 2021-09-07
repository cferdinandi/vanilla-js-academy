---
title: "Weights"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: false
---

{{<css>}}
#app {
	font-weight: bold
}
{{</css>}}

<p class="text-large" id="app" aria-live="polite">Loading...</p>

<button class="btn" data-weight="kg">Add 1 Kg</button> <button class="btn" data-weight="g">Add 5 grams</button> <button class="btn" data-weight="mg">Add 25 mg</button>

{{<js>}}
//
// Library stuff
//

let Weight = (function () {

	/**
	 * Standarize the weight into milligrams for easier math
	 * @param  {Number} weight The weight
	 * @param  {String} units  The units the weight is in
	 * @return {Number}        The weight in grams
	 */
	function weightToMg (weight, units) {
		weight = parseFloat(weight);
		if (units === 'grams') return weight * 1000;
		if (units === 'kg') return weight * 1000 * 1000;
		return weight;
	}

	/**
	 * The Constructor object
	 * @param {Number} weight The weight
	 * @param {String} units  The unit the weight is in
	 */
	function Constructor (weight, units) {

		// Make sure unit of weight is allowed
		if (!['mg', 'grams', 'kg'].includes(units)) {
			throw `"${units}" is not an allowed unit of weight.`;
		}

		// Define properties
		this._weight = weightToMg(weight, units);

	}

	/**
	 * Add milligrams to the weight
	 * @param {Number} n The weight to add
	 */
	Constructor.prototype.addMg = function (n) {
		this._weight = this._weight + parseFloat(n);
		return this;
	};

	/**
	 * Add grams to the weight
	 * @param {Number} n The weight to add
	 */
	Constructor.prototype.addGrams = function (n) {
		this._weight = this._weight + (parseFloat(n) * 1000);
		return this;
	};

	/**
	 * Add kilograms to the weight
	 * @param {Number} n The weight to add
	 */
	Constructor.prototype.addKg = function (n) {
		this._weight = this._weight + (parseFloat(n) * 1000 * 1000);
		return this;
	};

	/**
	 * Get weight in milligrams
	 * @param  {Boolean} format If true, return a formatted string
	 * @return {Number}         The weight in milligrams
	 */
	Constructor.prototype.inMg = function (format) {
		return format ? this._weight.toLocaleString() : this._weight;
	};

	/**
	 * Get weight in grams
	 * @param  {Boolean} format If true, return a formatted string
	 * @return {Number}         The weight in grams
	 */
	Constructor.prototype.inGrams = function (format) {
		let weight = this._weight / 1000;
		return format ? weight.toLocaleString() : weight;
	};

	/**
	 * Get weight in kilograms
	 * @param  {Boolean} format If true, return a formatted string
	 * @return {Number}         The weight in kilograms
	 */
	Constructor.prototype.inKg = function (format) {
		let weight = this._weight / 1000 / 1000;
		return format ? weight.toLocaleString() : weight;
	};

	return Constructor;

})();


//
// UI Stuff
//

let app = document.querySelector('#app');

/**
 * Display formatted weights in the UI
 */
function loadWeight () {
	app.textContent = `${scale.inKg(true)} kg - ${scale.inGrams(true)} grams - ${scale.inMg(true)} mg`;
}

/**
 * Add weight to scale
 * @param {Event} event The event object
 */
function addWeight (event) {

	// Only run for [data-weight] buttons
	let unit = event.target.getAttribute('data-weight');
	if (!unit) return;

	// Add weight
	if (unit === 'kg') {
		scale.addKg(1);
	} else if (unit === 'g') {
		scale.addGrams(5);
	} else if (unit === 'mg') {
		scale.addMg(25);
	}

	loadWeight();

}

let scale = new Weight(25, 'kg');
loadWeight();

document.addEventListener('click', addWeight);
{{</js>}}