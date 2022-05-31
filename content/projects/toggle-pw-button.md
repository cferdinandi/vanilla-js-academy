---
title: "Toggle Password Button"
date: 2018-01-11T16:03:33-05:00
draft: false
noIndex: true
---

{{<css>}}
[data-pw-toggle] .eye-visible,
[data-pw-toggle] .eye-hidden {
	height: 1em;
	width: 1em;
	margin-bottom: -0.125em;
}

[data-pw-toggle][aria-pressed="false"] .eye-visible {
	display: none;
}

[data-pw-toggle][aria-pressed="true"] .eye-hidden {
	display: none;
}
{{</css>}}

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

	<p>
		<button class="btn btn-secondary" type="button" data-pw-toggle="#password" aria-pressed="false">
			<svg xmlns="http://www.w3.org/2000/svg" class="eye-visible" width="1em" height="1em" viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="M8 12.797c-2.315 0-4.376-1.134-5.398-1.811-.685-.453-1.315-.972-1.775-1.461C.271 8.934 0 8.435 0 7.999s.27-.934.827-1.526c.46-.489 1.09-1.007 1.775-1.461C3.624 4.336 5.685 3.201 8 3.201s4.376 1.134 5.398 1.811c.685.453 1.315.972 1.775 1.461.556.591.827 1.09.827 1.525s-.27.934-.827 1.526c-.46.489-1.09 1.007-1.775 1.461-1.022.676-3.083 1.811-5.398 1.811zM1.526 8c.172.317.883 1.093 2.095 1.858.85.537 2.549 1.438 4.38 1.438s3.53-.901 4.38-1.438c1.212-.766 1.922-1.541 2.095-1.858-.172-.317-.883-1.093-2.095-1.858-.85-.537-2.549-1.438-4.38-1.438s-3.53.901-4.38 1.438C2.409 6.908 1.699 7.683 1.526 8zM6 8a2 2 0 1 1 3.999-.001A2 2 0 0 1 6 8z"/></svg>
			<svg xmlns="http://www.w3.org/2000/svg" class="eye-hidden" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="M2.905 11.18a13.171 13.171 0 0 1-.303-.194c-.685-.453-1.315-.972-1.775-1.461C.271 8.934 0 8.435 0 7.999s.27-.934.827-1.526c.46-.489 1.09-1.007 1.775-1.461C3.624 4.336 5.685 3.201 8 3.201c.197 0 .392.009.586.024L7.517 4.721c-1.642.137-3.122.928-3.897 1.418-1.212.766-1.922 1.542-2.095 1.858.172.317.883 1.093 2.095 1.858.05.032.103.065.159.098l-.874 1.223zm12.268-4.706c-.46-.489-1.09-1.007-1.775-1.461a12.125 12.125 0 0 0-2.209-1.156l1.794-2.512-.966-.69-10 14 .966.69 2.19-3.066c.85.301 1.814.518 2.827.518 2.315 0 4.376-1.134 5.398-1.811.685-.453 1.315-.972 1.775-1.461.556-.591.827-1.09.827-1.526s-.27-.934-.827-1.526zM12.38 9.858c-.85.537-2.549 1.438-4.38 1.438-.667 0-1.316-.12-1.913-.298l.907-1.27a2 2 0 0 0 2.314-3.24l.977-1.367c.88.307 1.619.72 2.094 1.02 1.212.766 1.922 1.541 2.095 1.858-.172.317-.883 1.093-2.095 1.858z"/></svg>
			Show Password
		</button>
	</p>

	<p>
		<button  class="btn btn-large" type="submit">Change Username</button>
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

	<p>
		<button class="btn btn-secondary" type="button" data-pw-toggle="#current-password, #new-password" aria-pressed="false">
			<svg xmlns="http://www.w3.org/2000/svg" class="eye-visible" width="1em" height="1em" viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="M8 12.797c-2.315 0-4.376-1.134-5.398-1.811-.685-.453-1.315-.972-1.775-1.461C.271 8.934 0 8.435 0 7.999s.27-.934.827-1.526c.46-.489 1.09-1.007 1.775-1.461C3.624 4.336 5.685 3.201 8 3.201s4.376 1.134 5.398 1.811c.685.453 1.315.972 1.775 1.461.556.591.827 1.09.827 1.525s-.27.934-.827 1.526c-.46.489-1.09 1.007-1.775 1.461-1.022.676-3.083 1.811-5.398 1.811zM1.526 8c.172.317.883 1.093 2.095 1.858.85.537 2.549 1.438 4.38 1.438s3.53-.901 4.38-1.438c1.212-.766 1.922-1.541 2.095-1.858-.172-.317-.883-1.093-2.095-1.858-.85-.537-2.549-1.438-4.38-1.438s-3.53.901-4.38 1.438C2.409 6.908 1.699 7.683 1.526 8zM6 8a2 2 0 1 1 3.999-.001A2 2 0 0 1 6 8z"/></svg>
			<svg xmlns="http://www.w3.org/2000/svg" class="eye-hidden" width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="M2.905 11.18a13.171 13.171 0 0 1-.303-.194c-.685-.453-1.315-.972-1.775-1.461C.271 8.934 0 8.435 0 7.999s.27-.934.827-1.526c.46-.489 1.09-1.007 1.775-1.461C3.624 4.336 5.685 3.201 8 3.201c.197 0 .392.009.586.024L7.517 4.721c-1.642.137-3.122.928-3.897 1.418-1.212.766-1.922 1.542-2.095 1.858.172.317.883 1.093 2.095 1.858.05.032.103.065.159.098l-.874 1.223zm12.268-4.706c-.46-.489-1.09-1.007-1.775-1.461a12.125 12.125 0 0 0-2.209-1.156l1.794-2.512-.966-.69-10 14 .966.69 2.19-3.066c.85.301 1.814.518 2.827.518 2.315 0 4.376-1.134 5.398-1.811.685-.453 1.315-.972 1.775-1.461.556-.591.827-1.09.827-1.526s-.27-.934-.827-1.526zM12.38 9.858c-.85.537-2.549 1.438-4.38 1.438-.667 0-1.316-.12-1.913-.298l.907-1.27a2 2 0 0 0 2.314-3.24l.977-1.367c.88.307 1.619.72 2.094 1.02 1.212.766 1.922 1.541 2.095 1.858-.172.317-.883 1.093-2.095 1.858z"/></svg>
			Show Password
		</button>
	</p>

	<p>
		<button class="btn btn-large" type="submit">Change Passwords</button>
	</p>
</form>

{{<js>}}
// Listen to all click events in the browser
document.addEventListener('click', function (event) {

	// Check if clicked item was a password toggle
	// If not, return and stop running the callback function
	if (!event.target.matches('[data-pw-toggle]')) return;

	// Get the password fields
	let selector = event.target.getAttribute('data-pw-toggle');
	let passwords = document.querySelectorAll(selector);

	// Get button state
	// If already pressed, deactivate it
	// Otherwise, activate it
	if (event.target.matches('[aria-pressed="true"]')) {
		event.target.setAttribute('aria-pressed', false);
	} else {
		event.target.setAttribute('aria-pressed', true);
	}

	// Loop through each password field
	for (let password of passwords) {

		// If the toggle is active, change the type to "text"
		// Otherwise, change it back to "password"
		if (event.target.matches('[aria-pressed="true"]')) {
			password.type = 'text';
		} else {
			password.type = 'password';
		}

	}

});
{{</js>}}