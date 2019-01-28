//
// Variables
//

var app, field;


//
// Methods
//

/**
 * Get the URL parameters
 * source: https://css-tricks.com/snippets/javascript/get-url-variables/
 * @param  {String} url The URL
 * @return {Object}     The URL parameters
 */
var getParams = function (url) {
	var params = {};
	var parser = document.createElement('a');
	url = url || window.location.href;
	parser.href = url;
	var query = parser.search.substring(1);
	var vars = query.split('&');
	for (var i=0; i < vars.length; i++) {
		var pair = vars[i].split("=");
		params[pair[0]] = decodeURIComponent(pair[1]);
	}
	return params;
};

/**
 * Get todo list data from localStorage
 * @return {Object} The todo list data
 */
var getTodos = function () {
	var saved = localStorage.getItem('todoLists');
	if (!saved) return {};
	return JSON.parse(saved);
};

/**
 * Save todo list data to localStorage
 * @param  {Object} lists The todo list data
 */
var saveTodos = function (lists) {
	if (!lists) return;
	localStorage.setItem('todoLists', JSON.stringify(lists));
};

/**
 * Render the initial homepage layout
 */
var renderHomepage = function () {

	// Create the component
	app = new Reef('[data-app]', {
		data: {
			lists: getTodos(),
			editing: null
		},
		template: function (props) {
			var val = props.editing ? ' value="' + props.editing + '"' : '';

			// Create the form
			var html =
				'<form id="add-list">' +
					'<label for="list-name">Create a new todo list</label>' +
					'<input type="text" id="list-name" autofocus="autofocus"' + val + '>' +
					'<p><button class="btn">' + (props.editing ? 'Edit' : 'Add') + ' List</button></p>' +
				'</form>';

			// If there are lists, create markup for them
			if (Object.keys(props.lists).length > 0) {
				html += '<h2>Lists</h2><ol>';
				for (var list in props.lists) {
					if (props.lists.hasOwnProperty(list)) {
						html +=
							'<li>' +
								'<a href="list.html?list=' + encodeURIComponent(list) + '">' + list + '</a> ' +
								'<button data-edit-list="' + list + '">Edit</button> ' +
								'<button data-delete-list="' + list + '">Delete</button>' +
							'</li>';
					}
				}
				html += '</ol>';
				html +=	'<p><button data-delete-all="lists">Delete All Lists</button></p>';
			}

			return html;

		}
	});

	// Do an initial render
	app.render();

	// Cache the new list field
	field = document.querySelector('#list-name');

};

/**
 * Render the initial list detail layout
 */
var renderList = function () {

	// Create the component
	app = new Reef('[data-app]', {
		data: {
			lists: getTodos(),
			list: getParams().list,
			editing: -1
		},
		template: function (props) {

			var html = '<p><a href="index.html">&larr; Back to all lists</a></p>';

			// Make sure there's a list
			if (!props.list || !props.lists[props.list]) return html + '<h1>Uh oh!</h1><p>This list cannot be found. Sorry!</p>';

			// Variables
			var list = props.lists[props.list];
			var val = list[props.editing] ? ' value="' + list[props.editing].item + '"' : '';

			// Create the form
			html +=
				'<h1>' + props.list + '</h1>' +
				'<form id="add-todo">' +
					'<label for="todo-item">What do you need to do?</label>' +
					'<input type="text" id="todo-item" autofocus="autofocus"' + val + '>' +
					'<p><button class="btn">' + (list[props.editing] ? 'Edit' : 'Add') + ' Todo</button></p>' +
				'</form>';

			// Create the list
			if (list.length > 0) {
				html += '<h2>Todos</h2><ul class="todos">';
				list.forEach(function (todo, index) {
					var completed = todo.completed ? ' class="completed"' : '';
					var checked = todo.completed ? ' checked="checked"' : '';
					html +=
						'<li class="todo">' +
							'<label data-todo="' + index + '"' + completed + '>' +
								'<input type="checkbox"' + checked + '>' +
								todo.item +
							'</label>' +
							' <button data-edit-todo="' + index + '">Edit</button>' +
							' <button data-delete-todo="' + index + '">Delete</button>' +
						'</li>';
				});
				html += '</ul>';
				html +=	'<p><button data-delete-all="todos">Delete All Todos</button></p>';
			}

			return html;
		}
	});

	// Do an initial render
	app.render();

	// Update the page title
	document.title = app.getData().list + ' | Todo';

	// Cache the new todo item field
	field = document.querySelector('#todo-item');

};

/**
 * Setup the DOM on page load
 */
var setup = function () {

	var page = document.querySelector('[data-app]').getAttribute('data-app');

	if (page === 'home') {
		renderHomepage();
		return;
	}

	if (page === 'list') {
		renderList();
	}

};

/**
 * Update the list key without changing the data order
 * @param  {Object} lists   The lists
 * @param  {String} editing The name of the list being edited
 * @return {Object}         The edited list data
 */
var getEditedList = function (lists, editing) {
	var editedLists = {};
	for (var key in lists) {
		if (lists.hasOwnProperty(key)) {
			if (key === editing) {
				editedLists[field.value] = lists[key].slice();
			} else {
				editedLists[key] = lists[key].slice();
			}
		}
	}
	return editedLists;
};

/**
 * Add new todo lists
 */
var addNewList = function () {

	// If there's no list name, bail
	if (field.value.length < 1) return;

	// Get the list data
	var lists = app.getData().lists;
	var editing = app.getData().editing;

	// Make sure list name doesn't already exist
	if (!editing && lists[field.value]) return;

	// If an existing list name is being edited, update it
	// Otherwise save the list name
	if (editing) {
		lists = getEditedList(lists, editing);
	} else {
		lists[field.value] = [];
	}

	// Update the state
	app.setData({
		lists: lists,
		editing: null
	});

	// Clear the field and refocus
	field.value = '';
	field.focus();

};

/**
 * Add new todo items
 */
var addNewTodo = function () {

	// If there's no todo item, bail
	if (field.value.length < 1) return;

	// If the todo item is being edited, update it
	// Otherwise save the todo item
	var lists = app.getData().lists;
	if (app.getData().editing > -1) {
		lists[app.getData().list][app.getData().editing].item = field.value;
	} else {
		lists[app.getData().list].push({
			item: field.value,
			completed: false
		});
	}

	// Update the state
	app.setData({
		lists: lists,
		editing: -1
	});

	// Clear the field and refocus
	field.value = '';
	field.focus();

};

/**
 * Handle form submission events
 */
var submitHandler = function (event) {

	// Add a new list
	if (event.target.matches('#add-list')) {
		event.preventDefault();
		addNewList();
	}

	// Add a new todo item
	if (event.target.matches('#add-todo')) {
		event.preventDefault();
		addNewTodo();
	}

};

/**
 * Handle render events
 */
var renderHandler = function () {
	if (!app) return;
	saveTodos(app.getData().lists);
};

/**
 * Update the view to edit a list
 * @param  {String} list The list to edit
 */
var editList = function (list) {
	if (!list) return;
	app.setData({editing: list});
	field.focus();
};

/**
 * Delete a list from state
 * @param  {String} list The list to delete
 */
var deleteList = function (list) {
	if (!list) return;
	var lists = app.getData().lists;
	delete lists[list];
	app.setData({lists: lists});
};

/**
 * Update the view to edit a todo list item
 * @param  {Number} todo The ID of the todo list item to edit
 */
var editTodo = function (todo) {
	if (todo < 0) return;
	app.setData({editing: todo});
	field.focus();
};

/**
 * Delete a todo list item
 * @param  {Number} todo The ID of the todo list item to edit
 */
var deleteTodo = function (todo) {
	if (todo < 0) return;
	var lists = app.getData().lists;
	if (!lists[app.getData().list][todo]) return;
	lists[app.getData().list].splice(todo, 1);
	app.setData({lists: lists});
};

// @done
/**
 * Change the completed status of a todo list item
 * @param  {Node} label The todo list item label in the DOM
 */
var toggleTodo = function (label) {

	// Get the checkbox for this item
	var input = label.querySelector('input');
	if (!input) return;

	// Get the todo item ID
	var todo = label.getAttribute('data-todo');

	// Get the todo list
	var lists = app.getData().lists;
	if (!lists[app.getData().list][todo]) return;

	// Update the "completed" state for the todo item
	lists[app.getData().list][todo].completed = input.checked;

	// Save the state
	app.setData({lists: lists});

};

/**
 * Delete all todo lists or todo items in a list
 * @param  {String} type The type of data to delete (valid: lists || todos)
 */
var deleteAll = function (type) {

	// Delete all lists
	if (type === 'lists') {
		if (!window.confirm('Do you really want to delete all of your lists?')) return;
		app.setData({lists: {}});
		return;
	}

	// Delete all todos
	if (type === 'todos') {
		if (!window.confirm('Do you really want to delete all of your todos?')) return;
		var lists = app.getData().lists;
		lists[app.getData().list] = [];
		app.setData({lists: lists});
	}
};

/**
 * Handle click events
 */
var clickHandler = function (event) {

	// If a todo list item was clicked
	var label = event.target.closest('[data-todo]');
	if (label) {
		toggleTodo(label);
		return;
	}

	// If an edit list button was clicked
	if (event.target.matches('[data-edit-list]')) {
		editList(event.target.getAttribute('data-edit-list'));
		return;
	}

	// If a delete list button was clicked
	if (event.target.matches('[data-delete-list]')) {
		deleteList(event.target.getAttribute('data-delete-list'));
		return;
	}

	// if an edit todo item button was clicked
	if (event.target.matches('[data-edit-todo]')) {
		editTodo(event.target.getAttribute('data-edit-todo'));
		return;
	}

	// If a delete todo item button was clicked
	if (event.target.matches('[data-delete-todo]')) {
		deleteTodo(event.target.getAttribute('data-delete-todo'));
	}

	// If a delete all button was clicked
	if (event.target.matches('[data-delete-all]')) {
		deleteAll(event.target.getAttribute('data-delete-all'));
	}

};

//
// Inits & Event Listeners
//

// Setup the DOM
setup();

// Event Listeners
document.addEventListener('submit', submitHandler, false);
document.addEventListener('render', renderHandler, false);
document.documentElement.addEventListener('click', clickHandler, false);