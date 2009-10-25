#!/usr/bin/python

from listseek import seek
from types import ModuleType

def gethelp(module, sstring):
	"""Help extracter. Searches for functions that contain sstring in name and calls help() method for them.
	module: a loaded Python module. Must be ModuleType.
	sstring: substring of function name"""
	# here i need to check if module is a module.
	if not isinstance(module, ModuleType):
		return 'First parameter is not a module.'
	if sstring == '':
		return 'Empty substring.'
	
	for func in seek(dir(module), sstring):
		help(module.__dict__[func])
