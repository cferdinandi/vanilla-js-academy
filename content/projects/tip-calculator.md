---
title: "Tip Calculator"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: true
---

<form id="calculator">

	<label for="amount">Amount</label>
	<input type="number" name="amount" id="amount" value="0" step=".01">

	<label for="tip">Tip (<em>as %</em>)</label>
	<input type="number" min="0" name="tip" id="tip" value="20">

	<label for="people">Number of People</label>
	<input type="number" min="1" name="people" id="people" value="1">

	<button class="btn">Calculate Tip</button>

</form>

<div id="result" aria-live="polite"></div>

{{<js>}}
// Get DOM elements
let calculator = document.querySelector('#calculator');
let result = document.querySelector('#result');

// Create currency formatter
let currency = new Intl.NumberFormat(undefined, {
	style: 'currency',
	currency: 'USD'
});

/**
 * Format currency
 * @param  {Number}  amount The amount of money
 * @return {String}         A formatted currency string
 */
function formatMoney (amount) {
	return currency.format(amount);
}

/**
 * Serialize all form data into an object
 * @param  {FormData} data The FormData object to serialize
 * @return {String}        The serialized form data
 */
function serialize (data) {
	let obj = {};
	for (let [key, value] of data) {
		if (obj[key] !== undefined) {
			if (!Array.isArray(obj[key])) {
				obj[key] = [obj[key]];
			}
			obj[key].push(value);
		} else {
			obj[key] = value;
		}
	}
	return obj;
}

/**
 * Calculate the tip
 * @param  {Event} event The submit event
 */
function submitHandler (event) {

	// Stop the page from reloading
	event.preventDefault();

	// Get the form data
	let {amount, tip, people} = serialize(new FormData(calculator));

	// Make sure all required fields are provided
	if (!amount || !tip || !people) {
		result.textContent = 'Please complete all fields.';
		return;
	}

	// Convert values to numbers
	amount = parseFloat(amount);
	tip = parseFloat(tip) / 100;
	people = parseInt(people, 10);

	// Calculate the total
	let tipAmount = amount * tip;
	let total = amount + tipAmount;

	// Show tip and total
	result.textContent = `Each person should tip ${formatMoney(tipAmount / people)} for a total of ${formatMoney(total / people)}.`;

}

// Listen for submit events
calculator.addEventListener('submit', submitHandler);
{{</js>}}