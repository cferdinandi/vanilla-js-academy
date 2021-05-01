---
title: "Counter"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: false
---

{{<css>}}
.count {
	font-size: 3em;
	margin-bottom: 0.5em;
}
{{</css>}}

<div id="app"></div>

This is powered by a DOM manipulation library you'll create that can be easily customized by developers like this.

```js
// Listen for custom counter:increase-before events
// Stop counting once you reach 20
document.addEventListener('counter:increase-before', function (event) {
	if (event.detail.count > 19) {
		event.preventDefault();
		alert(`You've reached the max allowed.`);
	}
});

// Create a new counter
let count = new Counter('#app', {
	buttonText: 'Increase By 1',
	start: 10
});
```

{{<js>}}
let Counter = (function () {

	// Default options
	let defaults = {
		displayClass: 'count',
		buttonClass: 'count-button',
		buttonText: 'Increase Count',
		start: 0
	};

	/**
	 * Emit a custom event
	 * @param  {String} type   The event type
	 * @param  {Object} detail Any details to pass along with the event
	 * @param  {Node}   elem   The element to attach the event to
	 */
	function emitEvent (type, detail = {}, elem = document) {

		// Make sure there's an event type
		if (!type) return;

		// Create a new event
		let event = new CustomEvent(type, {
			bubbles: true,
			cancelable: true,
			detail: detail
		});

		// Dispatch the event
		return elem.dispatchEvent(event);

	}

	/**
	 * Create counter display
	 * @param  {Object} settings The instance settings
	 * @return {Node}            The element
	 */
	function createDisplay (settings) {
		let display = document.createElement('div');
		display.setAttribute('aria-live', 'polite');
		display.className = settings.displayClass;
		display.textContent = settings.start;
		return display;
	}

	/**
	 * Create button to increase count
	 * @param  {Object} settings The instance settings
	 * @return {Node}            The element
	 */
	function createButton (settings) {
		let button = document.createElement('p');
		button.innerHTML = `<button class="${settings.buttonClass}">${settings.buttonText}</button>`;
		return button;
	}

	/**
	 * Increase count by n
	 * @param  {Instance} instance The specific instance
	 * @param  {Integer}  n        How much to increase the count by
	 */
	function increaseCount (instance, n = 1) {

		// Before event
		let canceled = !emitEvent('counter:increase-before', {count: instance._count}, instance._app);
		if (canceled) return;

		// Increase the count
		instance._count += n;
		instance._display.textContent = instance._count;

		// After event
		emitEvent('counter:increase', {count: this._count}, app);

	}

	/**
	 * Descrease count by n
	 * @param  {Instance} instance The specific instance
	 * @param  {Integer}  n        How much to decrease the count by
	 */
	function decreaseCount (instance, n = 1) {

		// Before event
		let canceled = !emitEvent('counter:decrease-before', {count: this._count}, app);
		if (canceled) return;

		// Decrease count
		instance._count -= n;
		instance._display.textContent = instance._count;

		// After event
		emitEvent('counter:decrease', {count: this._count}, app);

	}

	/**
	 * Create an event listener for a specific instance
	 * @param  {Node}          button   The button to listen for clicks on
	 * @param  {Instance}      instance The specific instance
	 * @return {EventListener}          The event listener
	 */
	function createListener (button, instance) {

		/**
		 * Handle click events
		 * @param {Event} event The event object
		 */
		function clickHandler (event) {
			if (!event.target.closest('button')) return;
			increaseCount(instance);
		}

		// Start the event listener
		button.addEventListener('click', clickHandler);

		return clickHandler;

	}

	/**
	 * The constructor object
	 * @param {String} selector The element to hold the counter app
	 * @param {Object} options  Options and settings
	 */
	function Constructor (selector, options = {}) {

		// Get the app element
		let app = document.querySelector(selector);
		if (!app) throw 'Element not found.';

		// Get the settings
		let settings = Object.assign({}, defaults, options);

		// Create UI elements
		let display = createDisplay(settings);
		let button = createButton(settings);

		// Inject them into the DOM
		app.append(display, button);

		// Listen for click events
		let listener = createListener(button, this);

		// Setup event
		emitEvent('counter:ready', {display, button, settings}, app);

		// Define properties
		Object.defineProperties(this, {
			_display: {value: display},
			_button: {value: button},
			_listener: {value: listener},
			_count: {value: settings.start, writable: true}
		});

	}

	/**
	 * Increase count by n
	 * @param  {Integer} n How much to increase the count by
	 */
	Constructor.prototype.increase = function (n) {
		increaseCount(this, n);
	};

	/**
	 * Decrease count by n
	 * @param  {Integer} n How much to increase the count by
	 */
	Constructor.prototype.decrease = function (n) {
		decreaseCount(this, n);
	};

	/**
	 * Reset the counter
	 */
	Constructor.prototype.reset = function () {

		// Before event
		let canceled = !emitEvent('counter:reset-before', {count: this._count}, app);
		if (canceled) return;

		// Reset count
		decreaseCount(this, this._count);

		// After event
		emitEvent('counter:reset', {count: this._count}, app);

	};

	/**
	 * Get the count
	 * @return {Integer} The current count
	 */
	Constructor.prototype.count = function () {
		return this._count;
	};

	/**
	 * Destroy the instance
	 */
	Constructor.prototype.destroy = function () {

		// Before event
		let canceled = !emitEvent('counter:destroy-before', {
			count: this._count,
			display: this._display,
			button: this._button
		}, app);
		if (canceled) return;

		// Reset the DOM
		this._button.removeEventListener('click', this._listener);
		this._button.remove();
		this._display.remove();
		this._count = null;

		// After event
		emitEvent('counter:destroy', {count: this._count}, app);

	};

	return Constructor;

})();

// Setup event
document.addEventListener('counter:increase-before', function (event) {
	if (event.detail.count > 19) {
		event.preventDefault();
		alert(`You've reached the max allowed.`);
	}
});

let count = new Counter('#app', {
	buttonText: 'Increase By 1',
	start: 10
});
{{</js>}}