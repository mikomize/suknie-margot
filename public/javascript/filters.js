var FilterAbstract = new Class({
	
	element : null,
	
	initialize : function() {
		this.createElement();
	},
	filter : function() {
		return false;
	},
	createElement : function() {
		
	}
});

var CheckboxFilterOrAgregator = new Class({
	Extends : FilterAbstract,
	
	filters : [],
	 
	registerFilter : function(filter) {
	  this.filters.push(filter);	
	},
	
	filter : function(item) {
		var any_checked = false;
    this.filters.each(function(filter) {
		  if(filter.valueContainer.checked == true) {
			  any_checked = true;
			}	
		});
		if(!any_checked) {
	    return true;
		}
		var valid = false;
		this.filters.each(function(filter) {
			if(filter.filter(item)) {
				valid = true;
			}			
		});
		return valid;
	} 
	
});

var InputFilterGeneric = new Class({
	
	Extends : FilterAbstract,
	
	fieldsToSearch : [],
	label : '',
	name : '',
  
	initialize : function(name, label, fieldsToSearch) {
		this.name = name;
		this.label = label;
		this.fieldsToSearch = fieldsToSearch;
		this.parent();
	},
	
	filter : function(item) {
		if(this.valueContainer.get('value') == '') {
			return true;
		}
		var show = false;
		$each(this.fieldsToSearch, function(key) {
			if(item[key].toLowerCase().contains(this.valueContainer.get('value').toLowerCase())) {
				show = true;
			}
		}.bind(this));
		return show;
	},
	
  createElement : function() {
		var label,input;
		label = new Element('label', {'text' : this.label, 'for' : this.label});
		input = new Element('input', {
			'type' : 'text',
			'class' : 'search-filter', 
			'id' : this.label, 
			'oninput' : 'this.onpropertychange()',
			'events' : {
				'click' : function() {
					this.set('value', '');
					this.onpropertychange();
				}
			}
		});
		input.onpropertychange = function() {
			window['eventManager'].fireEvent('filtersStateChanged');
		};
		label.grab(input);
		this.valueContainer = input;
		this.element = label;
	}
	
});

var RadioFilterGeneric = new Class({
  
  Extends : FilterAbstract,
  
  field : '',
  label : '',
  name : '',
  
  initialize : function(name, label, field, options) {
    this.name = name;
    this.label = label;
    this.field = field;
		this.options = options;
    this.parent();
  },
  
  filter : function(item) {
		if(this.value == 0) {
			return true;
		}
    if(this.value == item[this.field]) {
      return true;
    }
		return false;
  },
  
  createElement : function() {
    var container,label_el,input;
		var that = this;
		container = new Element('div');
		$each(this.options, function(label, value){
      labelElement = new Element('label', {
				'text' : label, 
				'for' : this.name + '-' + this.label,
				'class' : 'radio-filter-label'
			});
      input = new Element('input', {
        'id' : this.name + '-' + label,
				'name' : this.name,
				'class' : 'radio-filter', 
        'type' : 'radio',
				'value' : value,
				'events' : {
					'click' : function() {
						that.value = this.get('value');
						window['eventManager'].fireEvent('filtersStateChanged');
					}
				}
      }); 
			container.grab(input);
			container.grab(labelElement);
			container.grab(new Element('br')); 	
		}.bind(this));
    this.element = container;
  },
	
	setValue : function(value) {
		$each(this.element.getChildren(), function(item) {
			if(item.get('value') == value) {
				item.set('CHECKED', true);
				this.value = item.get('value');
			}
		}.bind(this));
	}
  
});

var CheckboxFilterGeneric = new Class({
  
  Extends : FilterAbstract,
  
  field : '',
  label : '',
  name : '',
  
  initialize : function(name, label, field, value) {
    this.name = name;
    this.label = label;
    this.field = field;
		this.value = value;
    this.parent();
  },
  
  filter : function(item) {
    if(this.valueContainer.checked == false) {
      return false;
    }
    var show = false;
    $each(item[this.field], function(key) {
      if(key == this.valueContainer.get('value')) {
        show = true;
      }
    }.bind(this));
    return show;
  },
  
  createElement : function() {
    var container,label,input;
		container = new Element('div');
    label = new Element('label', {'text' : this.label, 'for' : this.name, 'class' : 'tags-filter-label'});
    input = new Element('input', {
      'type' : 'checkbox',
      'class' : 'tags-filter',
			'value' : this.value, 
      'id' : this.name,
			'events' : {
			 'change' : function() {
			 	 this.onpropertychange();
			 }
			}
			
    });
		input.onpropertychange = function() {
      window['eventManager'].fireEvent('filtersStateChanged');
    };
    container.grab(input);
		container.grab(label);
    this.valueContainer = input;
    this.element = container;
  }
});
