/*
Based on Mootools 1.1 Slider.js
Author : Sylvain Philip

License:
	MIT-style license.
*/

/*
Class: BiSlider
	Creates a slider with tree elements: two knobs and a container. Returns the values.

Note:
	The Slider requires an XHTML doctype.

Arguments:
	element - the knobs container
	knobMin - the min handle
	knobMax - the max handle
	options - see Options below

Options:
	steps - the number of steps for your slider.
	mode - either 'horizontal' or 'vertical'. defaults to horizontal.
	offset - relative offset for knob position. default to 0.

Events:
	onChange - a function to fire when the value changes.
	onComplete - a function to fire when you're done dragging.
	onTick - optionally, you can alter the onTick behavior, for example displaying an effect of the knob moving to the desired position.
		Passes as parameter the new position.
*/

(function ($) {
	
	const capitalize = (s) => {
		if (typeof s !== 'string') return ''
		return s.charAt(0).toUpperCase() + s.slice(1)
	}
	
	var BiSlider = function(el, knobMin, knobMax, options) {
		
		this.options = $.extend({
			onChange: function(steps){},
			onComplete: function(step){},
			mode: 'horizontal',
			steps: 100,
			offset: 0
		}, options )
		
		this.element = $(el);
		this.element.css('position', 'relative');
		this.knobMin = $(knobMin);
		this.knobMax = $(knobMax);
		this.previousChange = -1;
		this.previousEnd = -1;
		this.step = -1;

		var mod, offset;

		switch(this.options.mode){
			case 'horizontal':
				this.z = 'x';
				this.p = 'left';
				mod = {'x': 'left', 'y': false};
				offset = 'offsetWidth';
				break;
			case 'vertical':
				this.z = 'y';
				this.p = 'top';
				mod = {'x': false, 'y': 'top'};
				offset = 'offsetHeight';
		}

		this.max = this.element[0][offset] - this.knobMin[0][offset] + (this.options.offset * 2);
		
		this.half = this.knobMin[0][offset]/2;
		this.getPos = this.element[0]['offset' + capitalize(this.p)];
		
		this.knobMin.css('position', 'absolute').css(this.p, - this.options.offset);
		this.knobMax.css('position', 'absolute').css(this.p, this.max);

		this.KnobMinWidth = this.knobMin[0][offset] + (this.options.offset * 2);
		this.knobMaxWidth = this.knobMax[0][offset] + (this.options.offset * 2);
		
		this.stepMin = this.toStep(- this.options.offset);
		this.stepMax = this.toStep(this.max);

		var that = this
		
		this.knobMin.draggable({
			cancel: false,
			containment: 'parent',
			axis: 'x',
			drag: function(event, ui) {
				var maxPosition = that.knobMax.position()[that.p]
				var minPosition = ui.position[that.p]
				
				if (minPosition > maxPosition) {
					return false
				}
				
				that.draggedKnob('dragMin');
			},
			stop: function(event, ui) {
				that.draggedKnob('dragMin');
				that.end()
			}
		})
		
		this.knobMax.draggable({
			cancel: false,
			containment: 'parent',
			axis: 'x',
			drag: function(event, ui) {
				var maxPosition = ui.position[that.p]
				var minPosition = that.knobMin.position()[that.p]
				
				if (maxPosition < minPosition) {
					return false
				}
				that.draggedKnob('dragMax');
			},
			stop: function(event, ui) {
				that.draggedKnob('dragMax');
				that.end()
			}
		})
	}

	BiSlider.prototype.draggedKnob = function(knob) {
		var maxPosition = this.knobMax.position()[this.p]
		var minPosition = this.knobMin.position()[this.p]

		if (knob == 'dragMax') {
			this.step = this.toStep(maxPosition);
			this.stepMax = this.step;
		} else {
			this.step = this.toStep(minPosition);
			this.stepMin = this.step;
		}

		this.checkStep();
	}

	BiSlider.prototype.checkStep = function() {
		if (this.previousChange != this.step){
			this.previousChange = this.step;
			var steps = {
				current : this.step,
				minimum : this.stepMin,
				maximum : this.stepMax 
			};

			this.options.onChange(steps)
		}
	}

	BiSlider.prototype.end = function(){
		if (this.previousEnd !== this.step){
			this.previousEnd = this.step;
			this.options.onComplete(this.step)
		}
	}

	BiSlider.prototype.toStep = function(position){
		return Math.round((position + this.options.offset) / this.max * this.options.steps)
	}

	$.fn.bislider = function(options) {
		return this.each(function() {
			var knobs = $(this).find('.knob')
			new BiSlider(this, knobs[0], knobs[1], options)
		})
	}

}(jQuery))

