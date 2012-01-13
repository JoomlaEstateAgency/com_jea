

//var jSonRequest = null;
var type_id = 0;
var department_id = 0;
var town_id = 0;


var optionsCallback = {
	onComplete: function(response) {
		
		if($('type_id') && $('type_id').getProperty('value') == '0') {
			$('type_id').empty();
			addOptionsList( $('type_id'), response.types);
		}
		
		if($('department_id') && $('department_id').getProperty('value') == '0') {
			$('department_id').empty();
			addOptionsList( $('department_id'), response.departments);
		}
	    
	    if ( $('town_id') && $('town_id').getProperty('value') == '0' ){
	    	$('town_id').empty();
	        addOptionsList( $('town_id'), response.towns);
	    }
	}
};

function refreshForm(){

	var formUrl = $('jea_search_form').getProperty('action') ;
	var cat = '';
	formUrl = formUrl.substring(0, formUrl.indexOf('?')) + '?option=com_jea&task=ajaxfilter';
	
	if($('renting') && $('selling')) {
		cat = $('renting').checked ? 'renting' : 'selling' ;
	} else {
		cat = $('cat').value;
	}
	var jSonRequest = new Json.Remote( formUrl+'&format=raw' , optionsCallback );
	jSonRequest.send({'cat': cat, 
                      'type_id': type_id, 
                      'town_id': town_id,
                      'department_id': department_id });
}

function reinitForm(){
	if($('type_id')){
		$('type_id').selectedIndex = 0;
	}
	if($('department_id')){
		$('department_id').selectedIndex = 0;
	}
	if($('town_id')){
		$('town_id').selectedIndex = 0;
	}
	type_id = 0;
	department_id = 0;
	town_id = 0;
	refreshForm();
}


function addOptionsList(selectElt, objectList){
	var value = eval(selectElt.name); //value of global var
	for(var i in objectList ){
		if(objectList[i].text){
			var optionsElt = new Element('option',{'value': objectList[i].value});
			if(objectList[i].value == value) {
				optionsElt.setProperty('selected', 'selected');
			}
			optionsElt.appendText(objectList[i].text);
			selectElt.appendChild(optionsElt);
		}
	}
}

function updateList(selectElt){
	
	switch(selectElt.name){
		case 'type_id' :
			type_id = selectElt.value;
			break;
		case 'town_id' :
			town_id = selectElt.value;
			break;
		case 'department_id' :
			department_id = selectElt.value;
			break;
	}
	
	if (type_id == 0 || town_id == 0 || department_id == 0) {
		refreshForm();	
	}
}
