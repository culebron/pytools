#!/usr/bin/python

import os, shutil, parse, safe, apachehost
from random import Random

parse.parser.usage = '%prog HOST TEMPLATE DEST'
bitrix_file = 'bitrix8setup.php'
parse.shovel({'name': 'force', 'help': 'Overwrite DEST if exists.', 'short': 'f', 'default': False}, {'name': 'title', 'help': 'Set page title', 'short': 't', 'default': 'Page title'})

if __name__ == '__main__':
	if len(parse.arguments) < 3:
		safe.quit('Please, provide host name, template name and destination filename.\n' + parse.parser.format_help(), 1)
	
	# check if template exists
	
	
	# check if destination doesn't exist or --force is on
	
	
	# 

