var ListItem = new Class({
	
	element : null,
	
  initialize : function(data) {
		this.data = data;
	  this.createElement();	
	},
	createElement : function() {
		var container = new Element('div', {
	    'class' : 'list-element'
	  });
		
		var indent = new Element('div', {'class' : 'indent'});
		indent.grab(new Element('div', {'class' : 'title'}));
		var topWrapper = new Element('div', {'class': 'top-wrapper'});
		var photoContainer = new Element('div', {'class' : 'photo-container'});
		var imgUri = this.data['primary_photo_id'] || 'no-photo.jpg';
		photoContainer.grab(new Element('img', {'src' : baseUrl + '/uploaded/thumbs/' + imgUri }));
		topWrapper.grab(photoContainer);
		var properties = new Element('div' , {'class' : 'properties'});
		var innerWrapper = new Element('div',{'class' : 'inner-wrapper'});
		innerWrapper.grab(new Element('strong', {'text' : 'Cena: '}));
		innerWrapper.appendText(this.data['price']/100 + 'zł');
		innerWrapper.grab(new Element('strong', {'text' : 'Rozmiary: '}));
    innerWrapper.appendText(this.data['sizes'].join(', '));
		properties.grab(innerWrapper);
		properties.grab(new Element('a', {
			'href' : '#nogo',
			'html' : '<span><span>Pokaż</span></span>',
			'class' : 'link',
			'events' : {
				'click' : function(e) {
					e.stop();
					window['eventManager'].fireEvent('showRollover', this.data);
				}.bind(this)
			}
 		}));
		topWrapper.grab(properties);
		indent.grab(topWrapper);
		indent.grab(new Element('strong', {'text' : this.data['name'], 'class' : 'padding1'}));
		indent.grab(new Element('p', {'text' : this.data['description']}));
		var wrapper;
		wrapper = new Element('div', {'class' : 'indent-wrapper'}).grab(indent);
    wrapper = new Element('div', {'class' : 'right-top'}).grab(wrapper);
		wrapper = new Element('div', {'class' : 'left-top'}).grab(wrapper);
		wrapper = new Element('div', {'class' : 'bot-border'}).grab(wrapper);
		wrapper = new Element('div', {'class' : 'right-border'}).grab(wrapper);
		wrapper = new Element('div', {'class' : 'left-border'}).grab(wrapper);
		wrapper = new Element('div', {'class' : 'box1'}).grab(wrapper);
		container.grab(wrapper);
		this.element = container;
	}	
});

var Rollover = new Class({
  photos : [],
	currentPhoto : 0,
  initialize : function(data) {
    this.data = data;
    $each(data.photos, function(item) {
      this.photos.push(item);
    }.bind(this));
  },
  createElement : function() {
    var rollover = new Element('div', {'class': 'rollover-container'});
    var photo = new Element('div', {'class' : 'rollover-photo'});
		var photoImg = new Element('img', {'width' : 450, 'height' : 600, 'src' : baseUrl + '/uploaded/' + this.data['primary_photo_id'] || 'no-photo.jpg'});
		this.photoImg = photoImg;
    photo.grab(photoImg);
    var photoNav = new Element('div', {'class' : 'photo-nav'});
		if(this.photos.length > 0) {
			photoNav.grab(new Element('span', {
		    'text' : '<< poprzednie',
				'class' : 'photo-prev',
				'events' : {
					'click' : function() {
						if(this.currentPhoto < 1) return;
						this.changePhoto(this.currentPhoto - 1);
					}.bind(this)
				}
			}));
			var photoCounter = new Element('span', {'class' : 'photo-counter', 'text' : this.currentPhoto+1 + ' / ' + this.photos.length });
			this.photoCounter = photoCounter; 
		  photoNav.grab(photoCounter);
			photoNav.grab(new Element('span', {
        'text' : 'następne >>',
        'class' : 'photo-next',
        'events' : {
          'click' : function() {
            if(this.currentPhoto >= this.photos.length-1) return;
            this.changePhoto(this.currentPhoto + 1);
          }.bind(this)
        }
      }));
		}
		photo.grab(photoNav);
    rollover.grab(photo);
		var rightContainer = new Element('div', {'class' : 'right-container'});
		var upperBox = new Element('div', {'class': 'product-upper-box'});
		var upperRight = new Element('div', {'class' : 'product-upper-right'});
		var upperLeft = new Element('div', {'class' : 'product-upper-left'});
		upperBox.grab(new Element('strong', {'text' : this.data['name']}));
		upperBox.grab(upperRight);
		upperBox.grab(upperLeft);
		upperRight.grab(new Element('strong', {'text' : 'Cena:'}));
		upperRight.grab(new Element('span', {'text': this.data['price'] / 100}));
		upperLeft.grab(new Element('strong', {'text' : 'Rozmiary:'}));
		var sizes = new Element('ul', {'class': 'product-sizes'});
		upperLeft.grab(sizes);
    $each(this.data['sizes'], function(value) {
			sizes.grab(new Element('li', {'text' : value}));
		});
		rightContainer.grab(upperBox);
		rightContainer.grab(new Element('strong', {'text' : 'Opis:'}));
		rightContainer.grab(new Element('div', {'class' : 'product-desc', 'text' : this.data['description']}));
		var closeLink = new Element('a', {'class' : 'link', 'href' : '#nogo', 'html' : '<span><span>Zamknij</span></span>'});
		closeLink.addEvent('click', function(e) {
			e.stop();
      window['eventManager'].fireEvent('hideRollover');
    });
		rightContainer.grab(new Element('div', {'class' : 'close-link-container'}).grab(closeLink));
		rollover.grab(rightContainer);
    rollover.addEvent('click', function(e) {
      e.stop();
    });
    rollover.fade('hide');
    return rollover;
  },
	changePhoto : function(index) {
		this.currentPhoto = index;
		this.photoImg.set('src', baseUrl + '/uploaded/' + this.photos[index]);
		this.photoCounter.set('text', this.currentPhoto+1 + ' / ' + this.photos.length );
	}
	
});

var DynamicList = new Class({
	
	requestUrl : baseUrl + '/galeria/json',
	
	items : [],
	element : null,
	filters : {},
	
	downloadData : function() {
		var request = this.getRequest();
		request.send();
	},
	
	getRequest : function() {
		var request = new Request.JSON({
			url : this.requestUrl,
			onSuccess : function(data){
				this.data = data;
	  	  $(window).addEvent('domready', this.initList.bind(this));
	    }.bind(this)
		});
		return request;
	},
	
	initList : function() {
		this.renderList();
		this.registerFilters();
		window['eventManager'].addEvent('filtersStateChanged', this.filter.bind(this));
		window['eventManager'].addEvent('showRollover', this.showRollover.bind(this));
		window['eventManager'].addEvent('hideRollover', this.hideRollover.bind(this));
		window['eventManager'].fireEvent('filtersStateChanged');
	},
	
	filter : function() {
		$each(this.items, function(item){
			var show = true;
	    $each(this.filters, function(filter,filterName) {
				show = show && filter.filter(item.data);
			}.bind(this));
			if(show == true) {
				item.element.setStyle('display', 'block');
			} else {
				item.element.setStyle('display', 'none');
			}
		}.bind(this));
	},
	
	renderList : function() {
		var element = $('list-container');
    $each(this.data.products, function(row) {
			var item = new ListItem(row);
			element.grab(item.element);
	    this.items.push(item);
		}.bind(this));
		this.element = element;
		this.panel = $('list-panel');
	},
	
	registerFilters : function() {
		this.filters['search-filter'] = new InputFilterGeneric('search-filter', 'Wyszukaj:',['name','description']);
		this.panel.grab(this.filters['search-filter'].element);
		var categories = {'0' : 'Wszystkie'};
		$extend(categories, this.data.categories);
		this.filters['category-filter'] = new RadioFilterGeneric('category-filter', 'Kategoria:','category_id', categories);
		this.filters['category-filter'].setValue(params['cat'] || 0);
		this.panel.grab(this.filters['category-filter'].element);
		this.panel.grab(new Element('p', {
      'text': 'Rozmiary: '
    }));
		this.filters['sizes-filter'] = new CheckboxFilterOrAgregator();
    $each(this.data.sizes, function(label,value) {
			var filter = new CheckboxFilterGeneric(label + '-tag-filter', label, 'sizes', value);
      this.filters['sizes-filter'].registerFilter(filter);
      this.panel.grab(filter.element);
    }.bind(this));
    this.panel.grab(new Element('p', {
		  'text': 'Dodatkowo szukaj w: '
	  }));
		
		this.filters['tags-filter'] = new CheckboxFilterOrAgregator();
		$each(this.data.tags, function(label,value) {
			var filter = new CheckboxFilterGeneric(label + '-tag-filter', label, 'tags', value);
			this.filters['tags-filter'].registerFilter(filter);
      this.panel.grab(filter.element);
			if(params['tag'] == value) {
			  filter.valueContainer.checked = 1;
			}
		}.bind(this));
		
		
	},
	
	rollover : null,
	
	getRollover : function(data) {
    rollover = new Rollover(data);
		return rollover.createElement();
	},
	
	showRollover : function(data) {
		if(this.rollover) {
		  this.rollover.destroy();
	  }
		this.rollover = this.getRollover(data);
	  $('main').grab(this.rollover);
		this.rollover.fade('in');
	},
	hideRollover : function() {
    if(this.rollover) {
			this.rollover.destroy();
		}
	}
});



var productsList = new DynamicList();
productsList.downloadData();