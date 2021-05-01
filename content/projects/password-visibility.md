---
title: "Password Visibility"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: false
---

<h2>Change Username</h2>

<p>Enter your username and password to change your username.</p>

<form>
	<div>
		<label for="username">Username</label>
		<input type="text" name="username" id="username">
	</div>

	<div>
		<label for="password">Password</label>
		<input type="password" name="password" id="password">
	</div>

	<div>
		<label for="show-password">
			<input type="checkbox" name="show-password" id="show-password" data-pw-toggle="#password">
			Show password
		</label>
	</div>

	<p>
		<button class="btn" type="submit">Change Username</button>
	</p>
</form>

<h2>Change Password</h2>

<p>Enter your current password and new password below.</p>

<form>
	<div>
		<label for="current-password">Current Password</label>
		<input type="password" name="current-password" id="current-password">
	</div>

	<div>
		<label for="new-password">New Password</label>
		<input type="password" name="new-password" id="new-password">
	</div>

	<div>
		<label for="show-passwords">
			<input type="checkbox" name="show-passwords" id="show-passwords" data-pw-toggle="#current-password, #new-password">
			Show passwords
		</label>
	</div>

	<p>
		<button class="btn" type="submit">Change Passwords</button>
	</p>
</form>

{{<js>}}
// Listen to all click events in the browser
document.addEventListener('click', function (event) {

	// Check if clicked item was a password toggle
	// If not, return and stop running the callback function
	if (!event.target.matches('[data-pw-toggle]')) return;

	// Check target password fields
	let passwords = document.querySelectorAll(event.target.getAttribute('data-pw-toggle'));

	// Loop through each password field
	for (let password of passwords) {

		// If the toggle is checked, change the type to "text"
		// Otherwise, change it back to "password"
		if (event.target.checked) {
			password.type = 'text';
		} else {
			password.type = 'password';
		}

	}

});
{{</js>}}