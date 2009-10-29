#!/usr/bin/python

import os, sys, shutil, parse

parse.parser.usage = '%prog HOST'
bitrix_file = 'bitrix8setup.php'
parse.shovel({'name': 'installer', 'help': 'Path to bitrix installer script', 'short': 'd', 'default': os.path.join(os.path.dirname(os.path.realpath(__file__)), bitrix_file)})

if __name__ == '__main__':
	#shutil.copy()
	pass
