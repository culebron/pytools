#!/usr/bin/python
import sys

def fopen(*args):
	try:
		return open(*args)
	except IOError:
		sys.exit('Error when tried to open file \'{0}\'. Error #{1[0]}: {1[1]}'.format(args[0], sys.exc_info()[1].args))

def quit(msg, status = 0):
	print msg
	sys.exit(status)

def catch(method, args, message):
	return catch_err(method, args, message, (IOError, OSError))

def catch_err(method, args, message, exceptions):
	if not isinstance(args, (list, tuple)):
		args = [args]
	
	if not isinstance(message, str):
		raise TypeError('message parameter must be a string')
	
	try:
		return method(*args)
	except exceptions:
		quit(message.format(*args) + '\nError #{0[0]}: {0[1]}'.format(sys.exc_info()[1].args), 1)

def deco(method, exceptions):
	pass
