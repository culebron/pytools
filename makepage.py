#!/usr/bin/python

import os, shutil, parse, safe, apachehost
from random import Random

tpl_suffix = '.nano.php'
default_title = 'Page title'

if __name__ == '__main__':
	parse.parser.usage = '%prog HOST TEMPLATE [DEST]'
	parse.shovel({'name': 'force', 'help': 'Overwrite DEST if exists.', 'short': 'f', 'default': False}, {'name': 'title', 'help': 'Set page title', 'short': 't', 'default': default_title})
	
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

	print 'Copying {0} to {1}.'
	
	# copy file
	safe.catch(shutil.copy, (src, dest), 'Can\'t copy template ({0}) to {1}.')
	
	print 'Ok.'
	
	# replace title with that from command line
	if parse.options['title'] != default_title:
		from contextlib import nested
		import re
		with nested(safe.fopen(dest), safe.fopen(dest + '.tmp', 'w') as (s, d):
			for i in s:
				d.write(re.replace(r'($APPLICATION\\-\\>SetTitle\\(\\s*")[^)]("\\))', '$1{0}$2'.format(parse.options['title'])))
	
	
