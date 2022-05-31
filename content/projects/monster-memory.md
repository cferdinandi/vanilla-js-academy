---
title: "Monster Memory"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: true
---

{{<css>}}
/**
 * Grid Layout
 */

#memory {
	display: grid;
	grid-template-columns: repeat(4, 1fr);
	grid-template-rows: repeat(3, 1fr);
	text-align: center;
	grid-column-gap: 0.5em;
	grid-row-gap: 0.5em;
}


/**
 * Tile Styles
 */

.tile {
	background-color: #e5e5e5;
	border: 1px solid #808080;
	font-size: 1.5em;
	font-weight: bold;
	height: 25vh;
	text-align: center;
}

.flipped {
	background-color: #f7f7f7;
}

#wins {
	font-weight: bold;
	margin-bottom: 0.5em;
	text-align: right;
}
{{</css>}}

<div class="screen-reader" id="narration" aria-live="polite"></div>
<div id="wins">0/6</div>
<div id="app">loading...</div>
<aside>
	<hr>
	<p class="text-small text-muted">Icons by <a href="https://thenounproject.com/term/door/311732/">Jamie Dickinson</a>, <a href="https://thenounproject.com/term/monster/184225/">Nicky Knicky</a>, <a href="https://thenounproject.com/term/monster/1510400/">Alvaro Cabrera</a>, <a href="https://thenounproject.com/term/monster/28460/">Eliricon</a>, <a href="https://thenounproject.com/term/monster/82823/">April Yang</a>, <a href="https://thenounproject.com/term/monster/1062009/">tk66</a>, <a href="https://thenounproject.com/term/monster/24990/">Alex WaZa</a>, <a href="https://thenounproject.com/term/monster/37212/">Husein Aziz</a>, <a href="https://thenounproject.com/term/monster/2236082">iconcheese</a>, and <a href="https://thenounproject.com/term/socks/38451/">Yazmin Alanis</a>.</p>
</aside>

{{<js>}}
//
// Variables
//

// The monsters and socks
let monsters = [
	{
		name: 'monster1',
		alt: 'A yellow monster with a curly nose'
	},
	{
		name: 'monster2',
		alt: 'A yellow monster with a wide head, one eye, and an underbite'
	},
	{
		name: 'monster3',
		alt: 'A green monster with eyes on stalks and a mouth at the top of its head'
	},
	{
		name: 'monster4',
		alt: 'A red monster with horns, four eyes, and no legs'
	},
	{
		name: 'monster5',
		alt: 'A green monster with three horns on each side of its head, one eye, and a sad look on its face'
	},
	{
		name: 'monster6',
		alt: 'A green, triangle-shaped monster with sharp teeth, walking upside-down on its hands'
	}
];

// Get the elements
let app = document.querySelector('#app');
let narrate = document.querySelector('#narration');
let wins = document.querySelector('#wins');

// Double the number of tiles
let tiles = [...structuredClone(monsters), ...structuredClone(monsters)];

// Card state
let matched = 0;
let flipped = [];
let timer;


//
// Methods
//

/**
 * Randomly shuffle an array
 * https://stackoverflow.com/a/2450976/1293256
 * @param  {Array} array The array to shuffle
 * @return {Array}       The shuffled array
 */
function shuffle (array) {

	let currentIndex = array.length;
	let temporaryValue, randomIndex;

	// While there remain elements to shuffle...
	while (0 !== currentIndex) {
		// Pick a remaining element...
		randomIndex = Math.floor(Math.random() * currentIndex);
		currentIndex -= 1;

		// And swap it with the current element.
		temporaryValue = array[currentIndex];
		array[currentIndex] = array[randomIndex];
		array[randomIndex] = temporaryValue;
	}

	return array;

}

/**
 * Render the board UI into the DOM
 */
function renderBoard () {

	// Shuffle the tiles
	shuffle(tiles);

	// Render the UI
	app.innerHTML =
		`<div id="memory">
			${tiles.map(function (tile, index) {
				return `<button class="tile" data-tile="${index}" aria-label="Card ${index + 1}"></button>`;
			}).join('')}
		</div>`;

}

/**
 * Get the index of the matching card, if it's already flipped
 * @param  {Integer} flippedIndex The index of the flipped card
 * @return {Integer}              The index of the matching card
 */
function getMatchIndex (flippedIndex) {
	return tiles.findIndex(function (tile, index) {
		if (index !== flippedIndex && tile.name === tiles[flippedIndex].name && tile.isFlipped) {
			return true;
		} else {
			return false;
		}
	});
}

/**
 * Hide a tile
 * @param  {Node}    tile  The tile to hide
 * @param  {Integer} index The tile index in the tiles array
 */
function hideTile (tile, index) {

	// Clear the content
	tile.innerHTML = '';

	// A11Y
	tile.setAttribute('aria-label', `Card ${index + 1}`);
	narrate.textContent = `Card ${index + 1} flipped back over`;

	// Toggle class
	tile.classList.remove('flipped');

	// Update flipped state
	tiles[index].isFlipped = false;

}

/**
 * Hide all visible tiles
 */
function hideTiles () {

	// Stop any future automatic hide from happening
	clearTimeout(timer);

	// Hide all tiles
	for (let {tile, index} of flipped) {
		hideTile(tile, index);
	}

	// Reset the flipped array
	flipped = [];

}

/**
 * Handle click events
 * @param  {Event} event The event object
 */
function clickHandler (event) {

	// Only run on tiles
	let tile = event.target.closest('[data-tile]');
	if (!tile) return;

	// If tile is disabled, do nothing
	if (tile.matches('[aria-disabled="true"]')) return;

	// Get tile index
	let index = parseFloat(tile.getAttribute('data-tile'));

	// If tile tile is flipped, hide it
	// Otherwise, flip it over
	if (tile.matches('.flipped')) {
		hideTile(tile, index);
	} else {

		// If two cards are already flipped, reset them
		if (flipped.length > 1) {
			hideTiles();
		}

		// Add the image
		tile.innerHTML = `<img alt="" src="/img/icons/${tiles[index].name}.svg">`;

		// A11Y
		tile.setAttribute('aria-label', tiles[index].alt);
		narrate.textContent = tiles[index].alt;

		// Toggle class
		tile.classList.add('flipped');

		// Update flipped state
		tiles[index].isFlipped = true;

		// Check if matching tile is already flipped
		let matchIndex = getMatchIndex(index);
		if (matchIndex > -1) {

			// Show number of matched cards
			matched++;
			wins.textContent = matched > 5 ? 'You won! ' : `${matched}/6`;

			// If all tiles are matched announce the win
			if (matched > 5) {
				narrate.textContent = 'You won!';
			}

			// Disable the current and matched card from being flipped
			let matchingTile = document.querySelector(`[data-tile="${matchIndex}"]`);
			tile.setAttribute('aria-disabled', true);
			matchingTile.setAttribute('aria-disabled', true);

			// Clear the flipped array
			flipped = [];

			// End the function
			return;

		}

		// Add current card to flipped array
		flipped.push({tile, index});

		// If two cards are flipped and not a match, automatically flip back over in a few seconds
		if (flipped.length > 1) {
			timer = setTimeout(function () {
				hideTiles();
			}, 1500);
		}

	}

}


//
// Inits & Event Listeners
//

renderBoard();
document.addEventListener('click', clickHandler);
{{</js>}}