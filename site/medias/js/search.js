

//var jSonRequest = null;
var type_id = 0;
var department_id = 0;
var town_id = 0;


var optionsCallback = {
	onComplete: function(response){
		var type_select = document.getElementById('type_id');
		var department_select = document.getElementById('department_id');
		var town_select = document.getElementById('town_id');
		
	    if ( type_select.value == '0'){
	        deleteChilds(type_select);
	        addOptionsList( type_select, response.types);
	    }
	    
	    if ( department_select.value == '0'){
	        deleteChilds(department_select);
	        addOptionsList( department_select, response.departments);
	    }
	    
	    if ( town_select.value == '0'){
	    	deleteChilds(town_select);
	        addOptionsList( town_select, response.towns);
	    }
	}
};

function refreshForm(){

	var formUrl = $('jea_search_form').action ;
    var cat = $('renting').checked ? 'renting' : 'selling' ;
	//alert(type_id);

	var jSonRequest = new Json.Remote( formUrl+'&format=raw' , optionsCallback );
	
	jSonRequest.send({'cat': cat, 
                      'type_id': type_id, 
                      'town_id': town_id,
                      'department_id': department_id });
	
}


function addOptionsList(selectElt, objectList){
	var type = selectElt.name ;
	
	var value = eval(type); //value of global var
	//alert(type+' : '+value);
	var optionsElt = null;
	for(var i in objectList ){
		if(objectList[i].text){
			var selected = false;
			if(objectList[i].value == value){
				selected = true;
			}
			optionsElt = createOption(objectList[i].value, objectList[i].text, selected);
			selectElt.appendChild(optionsElt);
		}
	}
}


function createOption(value, text, selected){
	var optionsElt = document.createElement("option");
	var optionsAttr = document.createAttribute("value");
	var optionsTxt = document.createTextNode(text);
	if(selected ===true){
		var optionselectedAttr = document.createAttribute("selected");
		optionselectedAttr.nodeValue = "selected";
		optionsElt.setAttributeNode(optionselectedAttr);
	}
	
	optionsAttr.nodeValue = value ;
	optionsElt.setAttributeNode(optionsAttr);
	optionsElt.appendChild(optionsTxt);
	return optionsElt;
}

function deleteChilds(Element){
	while(Element.lastChild){
		Element.removeChild(Element.lastChild);
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
	
	refreshForm();	
}
