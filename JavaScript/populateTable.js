/**
 * version alterada pelo ST Mathias em (Mar/19)
 */
// *********************Monta a tabela do menu modificar p editar os usuários****************** 
function popula(filter , id , remove ,fieldId ){
	var Table = JSON.parse(sessionStorage.getItem('usersTable'));        
	var fields = filter.split("=");
	// dicionário               
	var data  = { count: 0 };

	// The $.map() method applies a function to each item in an array or object and maps the results into a new array.
	// $.map( realArray, function( val, i ) { Do something });
	data["users"] = $.map( Table[fieldId] , function( n, i ) {
		// fields[1] = ".*"
		// "i": Perform case-insensitive matching
		// primeiro parâmetro de RegExp é uma string
		//RegExp constrói uma expressão regular
		  var patt = new RegExp(fields[1] , "i");
		// fields[0] = "cn"
		  var attr = fields[0];
		  var val = n[attr];                  
		  if(patt.test(val)){
			  data.count++;
			  return n;
		  }
	});
        
        /*if(remove == "res"){
		$.get('/SISCAU/templates/table_modificar.mst', function(tablevis) {
		    var rendered = Mustache.render(tablevis, data);
		    $(id).html(rendered);
		   // $("#Tabelaresumo").tablesorter();
		 });
	}*/

	if(remove){
		$.get('/SISCAU/templates/table_modificar.mst', function(tablevis) {
		    var rendered = Mustache.render(tablevis, data);
		    $(id).html(rendered);
		    $("#Tabela").tablesorter();
		 });
	}else{
		$.get('/SISCAU/templates/table_visualisar.mst', function(tablevis) {
		    var rendered = Mustache.render(tablevis, data);
		    $(id).html(rendered);
		    $("#Tabela").tablesorter();
		 });
	}
	
}

// *********************Monta a tabela do menu modificar p editar os usuários****************** 
function populares(filter , id , remove ,fieldId ){
	var Table = JSON.parse(sessionStorage.getItem('usersTable'));        
	var fields = filter.split("=");               
	var data  = { count: 0 };
	data["resumo"] = $.map( Table[fieldId] , function( n, i ) {
		  var patt = new RegExp(fields[1] , "i");
		  var attr = fields[0];
		  var val = n[attr];                  
		  if(patt.test(val)){
			  data.count++;
			  return n;
		  }
	});
        
        if(remove){
		$.get('/SISCAU/templates/table_resumo.mst', function(tablevis) {
		    var rendered = Mustache.render(tablevis, data);
		    $(id).html(rendered);
		   // $("#Tabelaresumo").tablesorter();
		 });
	}

	
	
}

// *********************Monta a tabela do menu Trocar Adm com a opcão de editar o Admin (inclusão ST Mathias)****************** 
function populaAdm(filter , id , remove ,fieldId ){
	var Table = JSON.parse(sessionStorage.getItem('usersTableAdm'));        
	var fields = filter.split("=");               
	var data  = { count: 0 };
	data["users"] = $.map( Table[fieldId] , function( n, i ) {
		  var patt = new RegExp(fields[1] , "i");
		  var attr = fields[0];
		  var val = n[attr];                 
		  if(patt.test(val)){
			  data.count++;
			  return n;                          
		  }
	});
	if(remove){
		$.get('/SISCAU/templates/table_modificarAdm.mst', function(tablevis) {
		    var rendered = Mustache.render(tablevis, data);
		    $(id).html(rendered);
		    $("#Tabela").tablesorter();
		 });
	}
	
}
