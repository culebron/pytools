#!/usr/bin/python

import os, shutil, parse, safe, apachehost
from random import Random

tpl_suffix = '.nano.php'

if __name__ == '__main__':
	parse.parser.usage = '%prog HOST TEMPLATE [DEST]'
	parse.shovel({'name': 'force', 'help': 'Overwrite DEST if exists.', 'short': 'f', 'default': False}, {'name': 'title', 'help': 'Set page title', 'short': 't', 'default': 'Page title'})
	
	if len(parse.arguments) < 2:
		safe.quit('Please, provide host name, template name and destination filename.\n' + parse.parser.format_help(), 1)
	
	# check if template exists
	src = os.path.join(os.path.dirname(os.path.realpath(__file__)), 'templates', parse.arguments[1] + tpl_suffix)
	
	if not os.path.isfile(src):
		safe.quit('There is no template {0} in templates folder ({1})'.format(parse.arguments[1], src), 1)
	
	# compose destination path
	dest = os.getcwd()
	if len(parse.arguments) > 2:
		dest = os.path.join(dest, parse.arguments[2])
	
	if os.path.isdir(dest):
		dest = os.path.join(dest, parse.arguments[1].replace(tpl_suffix, '.php'))
	
	if dest[-4:] != '.php':
		dest += '.php'
	
	# check if destination doesn't exist or --force is on
	if os.path.isfile(dest) and not parse.options['force']:
		safe.quit('Destination file already exists. Use -f/--force to overwrite.', 1)
	
	# copy file
	
