function HTTP () 
{
    this.response = null;
	this.status = null;
    this.error = null;
}

HTTP.prototype.getXmlHttp = function ()
{
	var xmlhttp = false;

	/* Compilation conditionnelle d'IE */
	/*@cc_on
	@if (@_jscript_version >= 5)
	 try
	 {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	 }
	 catch (e)
	 {
		try
		{
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch (E)
		{
		   xmlhttp = false;
		}
	 }
	 
	@else
	 xmlhttp = false;
	@end @*/

  /* on essaie de créer l'objet si ce n'est pas déjà fait */
  if (!xmlhttp && typeof XMLHttpRequest != 'undefined')
  {
     try
     {
        xmlhttp = new XMLHttpRequest();
     }
     catch (e)
     {
        xmlhttp = false;
     }
  }
  
  return xmlhttp;
}

//methode polymorphique pour se connecter
HTTP.prototype.send = function(request, responseListener)
{
    var p = this.getXmlHttp();
	if(!p){
		responseListener(p);
		return;
	}
    p.onreadystatechange = function (aEvt) { 
        if (p.readyState == 4) {
            if ( responseListener ){
                responseListener(p) ;
            }
        }
    };
    
    //p.onload = null;
    p.open(request.method, request.url, true); //Mode asynchrone
    if(request.header != null){
        for(var i=0; i < request.header.length; i++){
            p.setRequestHeader(request.header[i].key, request.header[i].value);
        }
    }
	try{
    	p.send(request.body);
         //jsdump( "debut requete :\n"+ "url: " + request.url + "\ncorp: " + request.body );
	} catch(e) {
		//alert(e) ;
	}
    
}



/*
methode GET classique
*/
function getRequest(baseUrl){
    this.url = ""+baseUrl;
    this.method = "GET";
    this.header = null;   
    this.body = null;
	this.parameters = false;
}

/*
insérer un parametre dans l'url
@param string key
@param string value
*/
getRequest.prototype.put = function (key, value){
    if ("string" != ( typeof key ) && "string" != ( typeof value )){
        throw "parametres incorrects pour la methode put de la classe getRequest";
        return;
    }
	if(this.parameters === false){
		this.url += "?"+key+"="+value ;
		this.parameters = true ;
	} else {
		this.url += "&"+key+"="+value ;
	}
}

/*
methode POST classique
*/
function postRequest(baseUrl) 
{
    this.url = baseUrl;
    this.method = "POST";
    this.header= [{key:"content-type",value:"application/x-www-form-urlencoded"}]; 
    this.body = null;
}

/*
inserer un parametre dans l'url
@param string key
@param string value
*/
postRequest.prototype.put = function (key, value){
    
    if ("string" != ( typeof key ) && "string" != ( typeof value )){
        throw "parametres incorrects pour la methode put de la classe postRequest";
        return;
    }
    if (this.body === null){
        this.body = key+"="+value ;
    } else {
        this.body += "&"+key+"="+value ;
    }
}

