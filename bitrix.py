#!/usr/bin/python

import os, shutil, parse, safe, apachehost
from random import Random

parse.parser.usage = '%prog HOST'
bitrix_file = 'templates/bitrix8setup.php'
parse.shovel({'name': 'installer', 'help': 'Path to bitrix installer script', 'short': 'd', 'default': os.path.join(os.path.dirname(os.path.realpath(__file__)), bitrix_file)})

if __name__ == '__main__':
	if len(parse.arguments) < 1:
		safe.quit('Please, provide host name\n' + parse.parser.format_help(), 1)
	
	# find apache host and get it's document root
	try:
		host = apachehost.find(apachehost.config_dir(), parse.arguments[0]).next() # we need only the first [0] host, since it has precedence in apache
	except StopIteration:
		safe.quit('Can\'t find host named {0}'.format(parse.arguments[0]), 1)
	
	newname = '{0}.php'.format(Random().randint(10**6, 10**7-1))
	
	docroot = os.path.join(os.path.normpath(apachehost.get(host, 'DocumentRoot')['DocumentRoot']), newname)
	
	# copy bitrix8setup.php to a randomly named php file
	safe.catch(shutil.copy, (parse.options['installer'], docroot), 'Can\'t copy bitrix installer to {1}.')
	
	# run default web browser
	os.system('x-www-browser http://{0}/{1}'.format(parse.arguments[0], newname))
	
	safe.quit('Success! Do installation instructions in your browser.')

