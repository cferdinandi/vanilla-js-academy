---
title: "Monster Game"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: false
---

{{<css>}}
/**
 * A simple grid layout
 */
.row {
	display: grid;
	grid-template-columns: auto auto auto;
	text-align: center;
}

.grid {
	min-height: 6em;
	padding: 1em;
}

/**
 * Scale image to the full width of the page
 */
.img-full {
	width: 100%;
}

/**
 * Style buttons to not look like buttons
 */
[data-monster] {
	background-color: transparent;
	border: 0;
}
{{</css>}}

<div id="status" class="screen-reader" aria-live="polite"></div>
<div id="app"></div>

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
	},
	{
		name: 'monster7',
		alt: 'A purple monster with a single, sad looking eye and tentacles for arms'
	},
	{
		name: 'monster8',
		alt: 'A purple, oval-shaped monster with one eye and no arms or legs'
	},
	{
		name: 'monster9',
		alt: 'A blue, insect-like monster, with bug eyes, three body sections, and a pair of wings'
	},
	{
		name: 'monster10',
		alt: 'A blue monster with lopsided eyes on stalks and long, sharp teeth'
	},
	{
		name: 'monster11',
		alt: 'A furry gray monster with long arms and a happy face'
	},
	{
		name: 'sock',
		alt: 'A pair of athletic socks'
	}
];

// Get the elements
let app = document.querySelector('#app');
let announce = document.querySelector('#status');

// Track the number of monsters found
let found = 0;


//
// Methods
//

/**
 * Randomly shuffle an array
 * https://stackoverflow.com/a/2450976/1293256
 * @param  {Array} array The array to shuffle
 * @return {String}      The first item in the shuffled array
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
 * Setup the UI to play the game
 */
function setupGame () {

	// Reset the number of monsters who have been found
	found = 0;

	// Shuffle the monsters array
	shuffle(monsters);

	// Inject the monsters into the DOM
	app.innerHTML =
		`<p>Click a door to reveal a monster. Try not to find the sock.</p>
		<div class="row">
			${monsters.map(function (monster, index) {
				return `
					<div class="grid">
						<button data-monster="${index}"><img alt="Door" src="/img/icons/door.svg"></button>
					</div>`;
			}).join('')}
		</div>`;

}

/**
 * Show the "you lost" message
 */
function showLost () {

	// Render the UI
	app.innerHTML =
		`<img class="img-full" alt="" src="https://media.giphy.com/media/13zUNhE9WZspMc/giphy.gif">
		<h2>Oops, you found a sock!</h2>
		<p>
			<button class="btn" data-play-again>Play Again</button>
		</p>`;

	// Make an announcement for screen readers
	announce.textContent = 'Oops, you found a sock! Click the button to play again.';

}

/**
 * Show the "you lost" message
 */
function showWon () {

	// Render the UI
	app.innerHTML =
		`<img class="img-full" alt="" src="https://media.giphy.com/media/1242bJFCbb3FxC/giphy.gif">
		<h2>You won!</h2>
		<p>You found all of the monsters. Congrats!</p>
		<p>
			<button class="btn" data-play-again>Play Again</button>
		</p>`;

	// Make an announcement for screen readers
	announce.textContent = 'You found all of the monsters. Congrats! Click the button to play again.';

}

/**
 * Handle click events on the play again button
 * @param  {Event} event The event object
 */
function playAgainHandler (event) {

	// Only run if play again button was clicked
	if (!event.target.hasAttribute('data-play-again')) return;

	// Reset the UI
	setupGame();

	// Let screen reader users know the game is ready
	announce.textContent = 'New game. Click a door to get started.';

}

/**
 * Handle click events on door buttons
 * @param  {Event} event The event object
 */
function doorHandler (event) {

	// Get the monster
	let btn = event.target.closest('[data-monster]');
	if (!btn) return;
	let monster = monsters[btn.getAttribute('data-monster')];
	if (!monster) return;

	// If a sock was found, show the lost message
	if (monster.name === 'sock') {
		showLost();
		return;
	}

	// Otherwise, increase the "monster found" count by 1
	found++;

	// If all monsters have been found, show the won message
	if (found === (monsters.length - 1)) {
		showWon();
		return;
	}

	// Create the monster image
	let img = document.createElement('img');
	img.src = `/img/icons/${monster.name}.svg`;
	img.alt = monster.alt;

	// Replace the button with the monster
	btn.replaceWith(img);

	// Announce the monster to screen readers
	announce.textContent = `Found: ${monster.alt}`;

}

/**
 * Handle click events
 * @param  {Event} event The event object
 */
function clickHandler (event) {
	doorHandler(event);
	playAgainHandler(event);
}


//
// Inits & Event Listeners
//

// Render the game board and reset values
setupGame();

// Listen for click events
document.addEventListener('click', clickHandler);
{{</js>}}