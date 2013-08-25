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

var BiSlider = new Class({
	
	Implements: [Events, Options],
	
	Binds: ['clickedElement', 'draggedKnob', 'scrolledElement'],

	options: {
		onChange: Class.empty,
		onComplete: Class.empty,
		onTick: function(pos){
			this.knobMin.setStyle(this.p, pos);
		},
		mode: 'horizontal',
		steps: 100,
		offset: 0
	},

	initialize: function(el, knobMin, knobMax, options){
		this.element = document.id(el);
		this.element.setStyle('position', 'relative');
		this.knobMin = document.id(knobMin);
		this.knobMax = document.id(knobMax);
		this.setOptions(options);
		this.previousChange = -1;
		this.previousEnd = -1;
		this.step = -1;
		// this.element.addEvent('mousedown', this.clickedElement.bindWithEvent(this));
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
		this.max = this.element[offset] - this.knobMin[offset] + (this.options.offset * 2);
		this.half = this.knobMin[offset]/2;
		this.getPos = this.element['get' + this.p.capitalize()].bind(this.element);
		this.knobMin.setStyle('position', 'absolute').setStyle(this.p, - this.options.offset);
		this.knobMax.setStyle('position', 'absolute').setStyle(this.p, this.max);
		this.KnobMinWidth = this.knobMin[offset] + (this.options.offset * 2);
		this.knobMaxWidth = this.knobMax[offset] + (this.options.offset * 2);
		
		this.stepMin = this.toStep(- this.options.offset);
		this.stepMax = this.toStep(this.max);
		
		var dragMinlim = {};
		var dragMaxlim = {};
		
		dragMinlim[this.z] = [- this.options.offset, this.max - this.knobMaxWidth - this.options.offset];
		dragMaxlim[this.z] = [this.KnobMinWidth - this.options.offset, this.max - this.options.offset];

		this.dragMin = new Drag(this.knobMin, {
			limit: dragMinlim,
			modifiers: mod,
			snap: 0,
			onStart: function(){
				this.draggedKnob('dragMin');
			}.bind(this),

			onDrag: function(){
				this.draggedKnob('dragMin');
			}.bind(this),

			onComplete: function(){
				this.draggedKnob('dragMin');
				this.end();
			}.bind(this)
		});

		this.dragMax = new Drag(this.knobMax, {
			limit: dragMaxlim,
			modifiers: mod,
			snap: 0,
			onStart: function(){
				this.draggedKnob('dragMax');
			}.bind(this),
			onDrag: function(){
				this.draggedKnob('dragMax');
			}.bind(this),
			onComplete: function(){
				this.draggedKnob('dragMax');
				this.end();
			}.bind(this)
		});
		
		if (this.options.initialize) this.options.initialize.call(this);
	},

	/*
	Property: set
		The slider will get the step you pass.

	Arguments:
		step - one integer
	*/

	set: function(step){
		this.step = step.limit(0, this.options.steps);
		this.checkStep();
		// this.end();
		// this.fireEvent('onTick', this.toPosition(this.step));
		return this;
	},

	clickedElement: function(event){
		/*
		var position = event.page[this.z] - this.getPos() - this.half;
		position = position.limit(-this.options.offset, this.max -this.options.offset);
		this.step = this.toStep(position);
		this.checkStep();
		this.end();
		this.fireEvent('onTick', position);
		*/
	},

	draggedKnob: function(knob){
		var dragMinValue = this.dragMin.value.now[this.z];
		var dragMaxValue = this.dragMax.value.now[this.z];
		
		var lim = {};

		if(knob == 'dragMax' ) {
			if(dragMinValue) {
				lim[this.z] = [dragMinValue + this.KnobMinWidth - this.options.offset, this.max - this.options.offset];
				this.dragMax.limit = lim;
			}
			this.step = this.toStep(dragMaxValue);
			this.stepMax = this.step;
		} else {
			if(dragMaxValue) {

				lim[this.z] = [- this.options.offset, dragMaxValue - this.knobMaxWidth - this.options.offset];
				this.dragMin.limit = lim;
			}
		    this.step = this.toStep(dragMinValue);
		    this.stepMin = this.step;
		}
		this.checkStep();
	},

	checkStep: function(){
		if (this.previousChange != this.step){
			this.previousChange = this.step;
			var steps = {
				current : this.step,
				minimum : this.stepMin,
				maximum : this.stepMax 
			};
				
			this.fireEvent('onChange', steps);
		}
	},

	end: function(){
		if (this.previousEnd !== this.step){
			this.previousEnd = this.step;
			this.fireEvent('onComplete', this.step + '');
		}
	},

	toStep: function(position){
		return Math.round((position + this.options.offset) / this.max * this.options.steps);
	},

	toPosition: function(step){
		return this.max * step / this.options.steps;
	}

});

