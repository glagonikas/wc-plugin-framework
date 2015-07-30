/* jshint node:true */
module.exports = function( grunt ) {
	'use strict';

	var config = {};

	// The makepot task extracts gettext messages from source
	// code and generates the POT file
	config.makepot = {
		framework: {
			options: {
				cwd: 'woocommerce',
				domainPath: 'i18n/languages',
				exclude: [],
				potFilename: 'sv-wc-plugin-framework.pot',
				mainFile: 'index.php',
				potHeaders: {
					'report-msgid-bugs-to': 'https://support.woothemes.com/hc/',
					'project-id-version': 'SkyVerge WooCommerce Plugin Framework <%= pkg.version %>',
				},
				processPot: function( pot ) {
					delete pot.headers['x-generator'];
					return pot;
				}, // jshint ignore:line
				type: 'wp-plugin',
				updateTimestamp: false
			}
		}
	};

	return config;
};
