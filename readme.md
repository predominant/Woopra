# Woopra Plugin #

This plugin automatically adds the snippet required to get [Woopra](http://woopra.com) tracking on your site.

## Installation ##

**Using Git:**

If your application is using git for version control, you can add this Woopra repository as a submodule. Do the following from your application directory:

	git submodule add git://github.com/predominant/Woopra.git Plugin/Woopra

If your application is not using git, you can still use git to clone the plugin, and this will simplify the updating process. Do the following from your application directory:

	cd Plugin
	git clone git://github.com/predominant/Woopra.git

**Using a zip file:**

Download the zip file, and extract to your application Plugin directory: `app/Plugin/Woopra`.

## Usage ##

Add the following to your AppController (or any controller you want to use it on):

	var $helpers = array(
		'Woopra.Woopra' => array(
			'domain' => 'mysite.com', // This option is mandatory
		)
	);

## Customising options ##

Various options are configurable.

Example:

	var $helpers = array(
		'Woopra.Woopra' => array(
			'domain' => 'mysite.com', // This option is mandatory
			'forceDomain' => true, // Force the domain tracked to be the domain configured on the line above
			'timeout' => 20, // Change the user timeout period to 20 minutes
			'query' => true, // Track the GET/query parameters as well
		)
	);
