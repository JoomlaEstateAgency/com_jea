/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate
 * agency
 * 
 * @copyright Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license GNU/GPL, see LICENSE.txt
 */

var JeaGateway = {

	startExport : function (gatewayId, gatewayTitle, webConsole) {

		var requestParams = {
			option: 'com_jea',
			format: 'json',
			task: 'gateway.export',
			id: gatewayId
		}
		
		var startMessage = Joomla.JText._('COM_JEA_EXPORT_START_MESSAGE').replace('%s', gatewayTitle)
	
		var startLine = webConsole.appendLine({text: startMessage})
	
		jQuery.getJSON( "index.php", requestParams)
			.done(function( json ) {
				
				if (json.error) {
					jQuery(startLine).addClass('error');
					jQuery(startLine).text('> ' + json.error);
					return;
				}
				
				var endMessage = Joomla.JText._('COM_JEA_EXPORT_END_MESSAGE')
					.replace('%s', gatewayTitle)
					.replace('%d', json.exported_properties)
				
				if (json.ftp_sent) {
					endMessage += ' ' + Joomla.JText._('COM_JEA_GATEWAY_FTP_TRANSFERT_SUCCESS')
				}
	
				endMessage += ' <a href="' + json.zip_url +'">' + Joomla.JText._('COM_JEA_GATEWAY_DOWNLOAD_ZIP') + '</a>'
	
				jQuery(startLine).html('> ' + endMessage)
				
				jQuery(document).trigger('gatewayActionDone');
			})
			.fail(function( jqxhr, textStatus, error ) {
				var err = textStatus + ", " + error
				jQuery(line).addClass('error');
				jQuery(line).text('> ' + "Request Failed: " + err)
			});
	},

	startImport : function(gatewayId, gatewayTitle, webConsole) {
		var startMessage = Joomla.JText._('COM_JEA_IMPORT_START_MESSAGE').replace('%s', gatewayTitle)

		var startLine = webConsole.appendLine({text: startMessage})
		var progressbar = webConsole.addProgressBar('import_bar_' + gatewayId);

		webConsole.addPlaceHolder('properties_found_'   + gatewayId);
		webConsole.addPlaceHolder('properties_updated_' + gatewayId);
		webConsole.addPlaceHolder('properties_created_' + gatewayId);
		webConsole.addPlaceHolder('properties_removed_' + gatewayId);

		JeaGateway.importRequest(gatewayId, gatewayTitle, startLine, webConsole);
	},

	importRequest : function(gatewayId, gatewayTitle, startLine, webConsole) {

		var requestParams = {
			option: 'com_jea',
			format: 'json',
			task: 'gateway.import',
			id: gatewayId
		}

		jQuery.getJSON( "index.php", requestParams)
			.done(function(response) {

				if (response.error) {
					jQuery(startLine).addClass('error');
					jQuery(startLine).text('> ' + response.error);
					return;
				}

				if (response.total == 0) {
					jQuery(startLine).text('> ' + 'Aucun bien à importer.');
					return;
				}

				var progressbar = webConsole.getProgressBar('import_bar_'+ gatewayId);

				if (progressbar.step == 0) {
					webConsole.getPlaceHolder('properties_found_' + gatewayId).empty().html(Joomla.JText._('COM_JEA_GATEWAY_PROPERTIES_FOUND').replace('%s', response.total));
					progressbar.options.steps = response.total;
				}

				webConsole.getPlaceHolder('properties_updated_' + gatewayId).empty()
					.html(Joomla.JText._('COM_JEA_GATEWAY_PROPERTIES_UPDATED').replace('%s', response.updated));
				webConsole.getPlaceHolder('properties_created_' + gatewayId).empty()
					.html(Joomla.JText._('COM_JEA_GATEWAY_PROPERTIES_CREATED').replace('%s', response.created));
				webConsole.getPlaceHolder('properties_removed_' + gatewayId).empty()
					.html(Joomla.JText._('COM_JEA_GATEWAY_PROPERTIES_DELETED').replace('%s', response.removed));

				progressbar.setStep(response.imported);

				if (response.total == response.imported) {
					var endMessage = Joomla.JText._('COM_JEA_IMPORT_END_MESSAGE').replace('%s', gatewayTitle)
					jQuery(startLine).html('> ' + endMessage)
					jQuery(document).trigger('gatewayActionDone')
					return;
				}

				// Recursive
				JeaGateway.importRequest(gatewayId, gatewayTitle, startLine, webConsole)
			})
			.fail(function( jqxhr, textStatus, error ) {
				var err = textStatus + ", " + error
				jQuery(startLine).addClass('error');
				jQuery(startLine).text('> ' + "Request Failed: " + err)
			});
	}

}

