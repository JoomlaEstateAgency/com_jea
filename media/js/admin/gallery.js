/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate
 * agency
 * 
 * @copyright Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license GNU/GPL, see LICENSE.txt
 */

window.addEvent('domready', function() {
	var sortOptions = {
		transition: Fx.Transitions.Back.easeInOut,
		duration: 700,
		mode: 'vertical',
		onComplete: function() {
			mySort.rearrangeDOM()
		}
	};

	var mySort = new Fx.Sort($$('ul.gallery li'), sortOptions);

	$$('a.delete-img').each(function(item) {
		item.addEvent('click', function() {
		this.getParent('li').destroy();
		mySort = new Fx.Sort($$('ul.gallery li'), sortOptions);
		});
	});

	$$('a.img-move-up').each(function(item) {
		item.addEvent('click', function() {
			var activeLi = this.getParent('li');
			if (activeLi.getPrevious()) {
				mySort.swap(activeLi, activeLi.getPrevious());
			} else if (this.getParent('ul').getChildren().length > 1 ) {
				// Swap with the last element
				mySort.swap(activeLi, this.getParent('ul').getLast('li'));
			}
		});
	});

	$$('a.img-move-down').each(function(item) {
		item.addEvent('click', function() {
		var activeLi = this.getParent('li');
			if (activeLi.getNext()) {
				mySort.swap(activeLi, activeLi.getNext());
			} else if (this.getParent('ul').getChildren().length > 1 ) {
				// Swap with the first element
				mySort.swap(activeLi, this.getParent('ul').getFirst('li'));
			}
		});
	});

});
