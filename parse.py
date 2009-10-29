#!/usr/bin/python

import sys
from optparse import OptionParser

parser = OptionParser()

def arguments(*args):
	if len(args) == 0:
		return
	
	if len(args) == 1 and hasattr(args[0], '__iter__'):
		args = args[0]
	
	for i in args:
		args = ['--'+i.pop('name'), 'short' in i.keys() and '-'+ i.pop('short', '') or '']
		parser.add_option(*args, **i)


