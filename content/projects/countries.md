---
title: "Countries API"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: true
---

{{<css>}}
@media (min-width: 30em) {
	.countries {
		display: grid;
		grid-template-columns: repeat(3, 1fr);
		grid-template-rows: 1fr;
		grid-column-gap: 1em;
	}
}

.country {
	margin-bottom: 1em;
}

summary {
	font-weight: bold;
	margin-bottom: 0.5em;
}
{{</css>}}

<div id="filters" hidden>
	<details>
		<summary>Filter Countries</summary>

		<label for="country-name">Country Name</label>
		<input type="text" id="country-name" data-filter="name">

		<label for="population">Population Size</label>
		<select data-filter="population" id="population">
			<option value="any" selected>Any</option>
			<option value="under10k">Under 10k</option>
			<option value="10k">10k and up</option>
			<option value="100k">100k and up</option>
			<option value="million">1 million and up</option>
			<option value="billion">1 billion and up</option>
		</select>

		<fieldset>
			<legend>Region</legend>

			<label>
				<input type="checkbox" data-filter="region" value="africa" checked>
				Africa
			</label>

			<label>
				<input type="checkbox" data-filter="region" value="americas" checked>
				Americas
			</label>

			<label>
				<input type="checkbox" data-filter="region" value="asia" checked>
				Asia
			</label>

			<label>
				<input type="checkbox" data-filter="region" value="europe" checked>
				Europe
			</label>

			<label>
				<input type="checkbox" data-filter="region" value="oceania" checked>
				Oceania
			</label>
		</fieldset>

	</details>
</div>
<div id="app"></div>

{{<js>}}
// Get DOM nodes
let app = document.querySelector('#app');
let filters = document.querySelector('#filters');

// Number formatter
let formatPopulation = new Intl.NumberFormat(undefined);

/**
 * Sanitize an HTML string
 * @param  {String}          str   The HTML string to sanitize
 * @param  {Boolean}         nodes If true, returns HTML nodes instead of a string
 * @return {String|NodeList}       The sanitized string or nodes
 */
function cleanHTML (str, nodes) {

	/**
	 * Convert the string to an HTML document
	 * @return {Node} An HTML document
	 */
	function stringToHTML () {
		let parser = new DOMParser();
		let doc = parser.parseFromString(str, 'text/html');
		return doc.body || document.createElement('body');
	}

	/**
	 * Remove <script> elements
	 * @param  {Node} html The HTML
	 */
	function removeScripts (html) {
		let scripts = html.querySelectorAll('script');
		for (let script of scripts) {
			script.remove();
		}
	}

	/**
	 * Check if the attribute is potentially dangerous
	 * @param  {String}  name  The attribute name
	 * @param  {String}  value The attribute value
	 * @return {Boolean}       If true, the attribute is potentially dangerous
	 */
	function isPossiblyDangerous (name, value) {
		let val = value.replace(/\s+/g, '').toLowerCase();
		if (['src', 'href', 'xlink:href'].includes(name)) {
			if (val.includes('javascript:') || val.includes('data:')) return true;
		}
		if (name.startsWith('on')) return true;
	}

	/**
	 * Remove potentially dangerous attributes from an element
	 * @param  {Node} elem The element
	 */
	function removeAttributes (elem) {

		// Loop through each attribute
		// If it's dangerous, remove it
		let atts = elem.attributes;
		for (let {name, value} of atts) {
			if (!isPossiblyDangerous(name, value)) continue;
			elem.removeAttribute(name);
		}

	}

	/**
	 * Remove dangerous stuff from the HTML document's nodes
	 * @param  {Node} html The HTML document
	 */
	function clean (html) {
		let nodes = html.children;
		for (let node of nodes) {
			removeAttributes(node);
			clean(node);
		}
	}

	// Convert the string to HTML
	let html = stringToHTML();

	// Sanitize it
	removeScripts(html);
	clean(html);

	// If the user wants HTML nodes back, return them
	// Otherwise, pass a sanitized string back
	return nodes ? html.childNodes : html.innerHTML;

}

/**
 * Get population string
 * @param  {Object} country Country data
 * @return {String}         List of country populations
 */
function getPopulations (country) {

	// Create populations array
	let populations = ['any'];

	// Add applicable populations
	if (country.population < 10000) {
		populations.push('under10k');
	}

	if (country.population > 9999) {
		populations.push('10k');
	}

	if (country.population > 99999) {
		populations.push('100k');
	}

	if (country.population > 999999) {
		populations.push('million');
	}

	if (country.population > 999999999) {
		populations.push('billion');
	}

	// Return space-delimited string
	return populations.join(' ');

}

/**
 * Show error if no countries to display
 */
function showNoCountries () {
	app.innerHTML = '<p>Unable to display countries at this time. Sorry!</p>';
}

/**
 * Show countries in the UI
 * @param  {Array} data The country data to display
 */
function showCountries (data) {
	app.innerHTML =
		`<div class="countries">
			${cleanHTML(data.map(function (country) {
				return `
					<div class="country" data-population="${getPopulations(country)}" data-region="${country.region.toLowerCase()}" data-name="${country.name.official.toLowerCase()}">
						<h2>${country.name.common}</h2>
						<img alt="The official flag of ${country.name.common}" src="${country.flags.png}">
						<ul>
							<li><strong>Population:</strong> ${formatPopulation.format(country.population)}</li>
							<li><strong>Capital:</strong> ${country.capital ? country.capital.join(', ') : 'None'}</li>
							<li><a aria-label="View ${country.name.common} in Google Maps" href="${country.maps.googleMaps}">View in Google Maps</a></li>
						</ul>
					</div>`;
			}).join(''))}
		</div>`;
}

/**
 * Get country data
 * @return {[type]} [description]
 */
function getCountries () {
	fetch('https://restcountries.com/v3.1/all').then(function (response) {
		if (response.ok) {
			return response.json();
		}
		throw response;
	}).then(function (data) {
		showCountries(data);
		filters.removeAttribute('hidden');
	}).catch(function (error) {
		console.warn(error);
		showNoCountries();
	});
}

/**
 * Handle click events
 * @param  {Event} event The event object
 */
function inputHandler (event) {

	// Only run on filters
	if (!event.target.matches('[data-filter]')) return;

	// Get the population and regions to include
	let population = document.querySelector('[data-filter="population"]').value;
	let regions = Array.from(document.querySelectorAll('[data-filter="region"]:checked')).map(function (input) {
		return input.value;
	});
	let name = document.querySelector('[data-filter="name"]').value.toLowerCase();

	// Get all countries
	let countries = document.querySelectorAll('.country');

	// Only show countries that meet filter criteria
	for (let country of countries) {
		if (
			country.matches(`[data-population~="${population}"]`) &&
			regions.includes(country.getAttribute('data-region')) &&
			(!name || country.getAttribute('data-name').includes(name))
		) {
			country.removeAttribute('hidden');
		} else {
			country.setAttribute('hidden', '');
		}
	}

}

// Initialize script
getCountries();
document.addEventListener('input', inputHandler);
{{</js>}}