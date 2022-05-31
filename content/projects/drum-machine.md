---
title: "Drum Machine"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: true
---

{{<css>}}
#drum-machine {
	display: grid;
	grid-template-columns: 1fr 1fr 1fr;
	grid-template-rows: 1fr 1fr 1fr;
	grid-column-gap: 0.3125em;
	grid-row-gap: 0.3125em;
	text-align: center;
}

button {
	background-color: #f7f7f7;
	border: 1px solid #e5e5e5;
	border-radius: 0.25em;
}

button:focus,
button:hover {
	background-color: #e5e5e5;
}

#drum-machine button {
	height: 20vh;
	width: 100%;
}

#drum-machine button.active {
	background-color: #0088cc;
	color: #ffffff;
}
{{</css>}}

<div id="drum-machine">
	<div>
		<button data-drum="y">
			<strong class="text-large">Ride</strong><br>
			Y
		</button>
	</div>
	<div>
		<button data-drum="u">
			<strong class="text-large">Hi-Hat 1</strong><br>
			U
		</button>
	</div>
	<div>
		<button data-drum="i">
			<strong class="text-large">Hi-Hat 2</strong><br>
			I
		</button>
	</div>
	<div>
		<button data-drum="h">
			<strong class="text-large">Perc</strong><br>
			H
		</button>
	</div>
	<div>
		<button data-drum="j">
			<strong class="text-large">Kick 1</strong><br>
			J
		</button>
	</div>
	<div>
		<button data-drum="k">
			<strong class="text-large">Kick 2</strong><br>
			K
		</button>
	</div>
	<div>
		<button data-drum="b">
			<strong class="text-large">Clap</strong><br>
			B
		</button>
	</div>
	<div>
		<button data-drum="n">
			<strong class="text-large">Snare 1</strong><br>
			N
		</button>
	</div>
	<div>
		<button data-drum="m">
			<strong class="text-large">Snare 2</strong><br>
			M
		</button>
	</div>
</div>

{{<js>}}
// Drums and their matching sounds
let drums = {
	y: 'ride',
	u: 'hihat1',
	i: 'hihat2',
	h: 'perc',
	j: 'kick1',
	k: 'kick2',
	b: 'clap',
	n: 'snare1',
	m: 'snare2'
};

/**
 * Play the sound associated with the drum
 * @param  {String} drum The drum to play
 */
function playSound (drum) {

	// Make sure drum exists
	if (!drums[drum]) return;

	// Create a new Audio object and play the sound
	let sound = new Audio(`/img/mp3s/${drums[drum]}.mp3`);
	sound.play();

	// If prefers-reduced-motion is enabled do nothing else
	// Otherwise, flash color on the button
	if (window.matchMedia('(prefers-reduced-motion)').matches) return;

	// Get the matching button
	let btn = document.querySelector(`[data-drum="${drum}"]`);

	// Add the .active class
	btn.classList.add('active');

	// Remove it after 1/10th of a second
	setTimeout(function () {
		btn.classList.remove('active');
	}, 100);

}

/**
 * Play drum on click
 * @param  {Event} event The event object
 */
function clickHandler (event) {

	// Only run on drum buttons
	let btn = event.target.closest('[data-drum]');
	if (!btn) return;

	// Get the matching instrument
	let drum = btn.getAttribute('data-drum');

	// Play the sound
	playSound(drum);

}

/**
 * Play drum on key events
 * @param  {Event} event The event object
 */
function keyHandler (event) {

	// Get the drum
	let drum = event.code.slice(3).toLowerCase();

	// Play the sound
	playSound(drum);

}

// Listen for events
document.addEventListener('click', clickHandler);
document.addEventListener('keydown', keyHandler);
{{</js>}}