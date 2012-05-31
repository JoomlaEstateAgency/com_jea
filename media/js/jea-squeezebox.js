

var onOpenSqueezebox = function(content) {

	var imgLinks = $$('a.jea_modal');
	var gallery = [];
	var currentIndex = 0;

	imgLinks.each(function(el, count) {

		if (el.href.test(SqueezeBox.url)) {
			currentIndex = count;
		}
		var ImgElt = el.getElement('img');
		if (ImgElt) {
			gallery[count] = {
				title : ImgElt.getProperty('alt'),
				description : ImgElt.getProperty('title'),
				url : el.href
			};
		}
	});
	
	var replaceImage = function (imageIndex) {
		if (!gallery[imageIndex]) {
			return false;
		}
		content.empty();
		var newImage = gallery[imageIndex];

		// This override the non-wanted opacity effect once the gallery is
		// loaded
		SqueezeBox.fx.content = new Fx.Tween(content, {
			property: 'min-height',
			duration: 150,
			link: 'cancel'	
		}).set(0);
		
		SqueezeBox.setContent('image', newImage.url);
		appendInfos(newImage);
		return true;
	};

	var appendInfos = function(imgData) {
		if (imgData.title || imgData.description) {
			var infosElt = document.id('jea-squeezeBox-infos');

			if (!infosElt) {
				infosElt = new Element('div', {'id' : 'jea-squeezeBox-infos'})
				content.getParent().adopt(infosElt);
			} else {
				infosElt.empty();
			}

			if (imgData.title) {
				infosElt.adopt(new Element('div', {
					'id' : 'jea-squeezeBox-title',
					'text' : imgData.title
				}));
			}
			
			if (imgData.description) {
				infosElt.adopt(new Element('div', {
					'id' : 'jea-squeezeBox-description',
					'text' : imgData.description
				}));
			}
		}
	};

	var navBlock = document.id('jea-squeezeBox-navblock');

	if (!navBlock) {
		var navBlock = new Element('div', { 'id' : 'jea-squeezeBox-navblock' });
		content.getParent().adopt(navBlock);
	}

	if (!document.id('jea-squeezeBox-prev')) {

		if (!previousLabel) {
			previousLabel = 'Previous';
		}

		var previousLink = new Element('a', {
			'href' : '#',
			'id' : 'jea-squeezeBox-prev',
			'text' : '< ' + previousLabel
		});

		previousLink.addEvent('click', function(e) {
			e.stop();
			if (replaceImage(currentIndex-1)) {
				currentIndex--;
				if (!gallery[currentIndex-1]) {
					this.setProperty('class', 'inactive')
				}
			}
			
			if (gallery[currentIndex+1] && nextLink.getProperty('class') == 'inactive') {
				nextLink.removeProperty('class');
			}
		});

		navBlock.adopt(previousLink);

	} else {
		var previousLink = document.id('jea-squeezeBox-prev')
	}
	
	if (!document.id('jea-squeezeBox-next')) {

		if (!nextLabel) {
			nextLabel = 'Next';
		}

		var nextLink = new Element('a', {
			'href' : '#',
			'id' : 'jea-squeezeBox-next',
			'text' : nextLabel+' >'
		});

		nextLink.addEvent('click', function(e) {
			e.stop();
			if (replaceImage(currentIndex+1)) {
				currentIndex++;
				if (!gallery[currentIndex+1]) {
					this.setProperty('class', 'inactive')
				}
			}

			if (gallery[currentIndex-1] && previousLink.getProperty('class') == 'inactive') {
				previousLink.removeProperty('class');
			} 
		});

		navBlock.adopt(nextLink);
	} else {
		var nextLink = document.id('jea-squeezeBox-next')
	}

	if (!gallery[currentIndex-1]) {
		previousLink.setProperty('class', 'inactive');
	}

	if (!gallery[currentIndex+1]) {
		nextLink.setProperty('class', 'inactive');
	}

	appendInfos(gallery[currentIndex]);
	
}
