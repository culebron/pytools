#!/usr/bin/python

import sys
from optparse import OptionParser

parser = OptionParser()
options = None
arguments = None

def shovel(*args):
	if len(args) == 0:
		return
	
	if len(args) == 1 and isinstance(args[0], (list, tuple)):
		args = args[0]

	for i in args:
		args = ['--'+i.pop('name'), 'short' in i.keys() and '-'+ i.pop('short', '') or '']
		parser.add_option(*args, **i)

	global options, arguments
	(options, arguments)  = parser.parse_args()


