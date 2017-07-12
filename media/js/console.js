/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate
 * agency
 * 
 * @copyright Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license GNU/GPL, see LICENSE.txt
 */

+function ($) {

	'use strict';
	
	// PROGRESSBAR CLASS DEFINITION
	// ============================
	
	var ProgressBar = function (element, options) {
		this.progressBar = $(element);
		this.options = options;
		this.timer = null;
		this.startTime = null;
		this.step = 0;
	}
	
	ProgressBar.DEFAULTS = {
		steps : 100,
		color : '#fff',
		size : 30
	}

	ProgressBar.prototype.setStep = function(step) {

		// if we go over our bound, just ignore it
		if (step > this.options.steps) {
			return;
		}

		this.step = step;

		var date = new Date();

		if (!this.startTime) {
			this.startTime = date.getTime() / 1000;
		}

		var now = date.getTime() / 1000;

		var perc = this.step / this.options.steps;

		var bar = Math.floor(perc * this.options.size);

		var status_bar = "[";

		for (var i = 0; i <= bar; i++) {
			status_bar += '=';
		}

		if (bar < this.options.size) {
			status_bar += '>';
			for (var i = 0; i <= this.options.size - bar; i++) {
				status_bar += '\u00A0'; // Unicode non-breaking space
			}
		} else {
			status_bar += '=';
		}

		var disp = Math.floor(perc * 100);

		status_bar += "] " + disp + "%  " + this.step + "/" + this.options.steps;

		var rate = (now - this.startTime) / this.step;
		var left = this.options.steps - this.step;

		var remaining = Joomla.JText._('COM_JEA_GATEWAY_IMPORT_TIME_REMAINING', 'Time remaining: %d sec.');
		var elapsed = Joomla.JText._('COM_JEA_GATEWAY_IMPORT_TIME_ELAPSED', 'Time elapsed: %d sec.');
		
		status_bar += "  " + remaining.replace('%d', Math.round(rate * left))
		status_bar += "  " + elapsed.replace('%d', Math.round(now - this.startTime))

		this.progressBar.text(status_bar);

	};

	ProgressBar.prototype.gotToNextStep = function() {
		this.step++;
		this.setStep(this.step);
	};
	

	// CONSOLE CLASS DEFINITION
	// ========================

	var Console = function (element, options) {
		this.console = $(element);
		this.options = options;
		this.progressBars = [];
	}


	Console.prototype.appendLine = function(message, prefix) {
		var text = prefix || '> ';
		var className = 'info';
		if (message.error) {
			text += message.error;
			className = 'error';
		}

		if (message.warning) {
			text += message.warning;
			className = 'warning';
		}

		if (message.text)
			text += message.text;

		var line = $('<p>', {
			class: className,
			text: text.toString()
		});
		this.console.append(line);
		return line;
	};
	
	Console.prototype.addPlaceHolder = function(name) {
		var line = $('<p>', {
			class: 'placeholder',
			id: name
		});
		this.console.append(line);
		return line;
	};

	Console.prototype.getPlaceHolder = function(name) {
		return $('#'+name);
	};
	
	Console.prototype.clear = function() {
		this.progressBars = null;
		this.console.empty();
	};
	
	Console.prototype.addProgressBar = function(name, options) {
		var progressbarContainer = $('<div>', {
			class: 'progressbar',
			id: name
		});
		var options  = $.extend({}, ProgressBar.DEFAULTS, typeof options == 'object' && options)
		this.console.append(progressbarContainer);
		this.progressBars[name] = new ProgressBar(progressbarContainer, options);
		return this.progressBars[name];
	};
	
	Console.prototype.getProgressBar = function(name) {
			if (this.progressBars[name]) {
				return this.progressBars[name];
			}
			return false;
	};
	
	// CONSOLE PLUGIN DEFINITION
	// ============================

	function Plugin(options) {
		var $this   = $(this)
		var instance    = $this.data('jea.console')
		if (!instance) $this.data('jea.console', (instance = new Console(this, options)))
		return instance;
	}

	var old = $.fn.console

	$.fn.console             = Plugin
	$.fn.console.Constructor = Console

	// CONSOLE NO CONFLICT
	// ====================

	$.fn.console.noConflict = function() {
		$.fn.console = old
		return this
	}

}(jQuery);




