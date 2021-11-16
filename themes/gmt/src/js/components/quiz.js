function runQuiz () {

	// Get elements
	let quiz = document.querySelector('[data-submit="quiz"]');
	if (!quiz) return;
	let notification = quiz.querySelector('#notify');
	if (!notification) return;

	// Show notification message in the UI
	function notify (msg) {
		notification.innerHTML = msg;
	}

	// Serialize form data to an object
	function serialize () {
		let data = new FormData(quiz);
		let obj = {};
		for (let [key, value] of data) {
			obj[key] = value;
		}
		return obj;
	}

	// Handle submit events
	function submitHandler () {

		// Don't reload the page
		event.preventDefault();

		// Get form data
		let {previous, skills} = serialize();

		// If either question is unanswered, show an error
		if (!previous || !skills) {
			notify('Please answer all questions.');
			return;
		}

		// If the user has a good grasp of the fundamentals or attended a previous Academy
		// Recommend advanced
		if ((skills !== 'not' && previous === 'yes') || skills === 'very') {
			notify(`The <a href="/advanced">Advanced JS: Structure & Scale workshop</a> is the best fit for you.`);
			return;
		}

		// Otherwise, recommend essentials
		notify(`The <a href="/essentials">Vanilla JS Essentials workshop</a> is the best fit for you. ${previous === 'yes' ? `Contact Chris at <a href="mailto:&#099;&#104;&#114;&#105;&#115;&#064;&#103;&#111;&#109;&#097;&#107;&#101;&#116;&#104;&#105;&#110;&#103;&#115;&#046;&#099;&#111;&#109;?subject=Vanilla%20JS%20Academy%3A%20Alumni%20Pricing&body=I'm%20interested%20in%20attending%20the%20Vanilla%20JS%20Academy%20again.%20Can%20you%20provide%20me%20with%20special%20alumni%20pricing%3F%20The%20email%20I%20used%20to%20enroll%20last%20time%20was%20%7BEMAIL%7D.">&#099;&#104;&#114;&#105;&#115;&#064;&#103;&#111;&#109;&#097;&#107;&#101;&#116;&#104;&#105;&#110;&#103;&#115;&#046;&#099;&#111;&#109;</a> about special alumni pricing.` : ''}`);

	}

	quiz.addEventListener('submit', submitHandler);

}

export default runQuiz;