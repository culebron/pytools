#!/usr/bin/python
import sys
from types import FunctionType, BuiltinFunctionType

def require(arg_name, *allowed_types):
	def make_wrapper(f):
		if hasattr(f, "wrapped_args"):
			wrapped_args = getattr(f, "wrapped_args")
		else:
			code = f.func_code
			wrapped_args = list(code.co_varnames[:code.co_argcount])

		try:
			arg_index = wrapped_args.index(arg_name)
		except ValueError:
			raise NameError, arg_name

		def wrapper(*args, **kwargs):
			if len(args) > arg_index:
				arg = args[arg_index]
				if not isinstance(arg, allowed_types):
					type_list = " or ".join(str(allowed_type) for allowed_type in allowed_types)
					raise TypeError, "Expected '%s' to be %s; was %s." % (arg_name, type_list, type(arg))
			else:
				if arg_name in kwargs:
					arg = kwargs[arg_name]
					if not isinstance(arg, allowed_types):
						type_list = " or ".join(str(allowed_type) for allowed_type in allowed_types)
						raise TypeError, "Expected '%s' to be %s; was %s." % (arg_name, type_list, type(arg))

			return f(*args, **kwargs)

		wrapper.wrapped_args = wrapped_args
		return wrapper

	return make_wrapper

def fopen(*args):
	try:
		return open(*args)
	except (OSError, IOError):
		sys.exit('Error when tried to open file \'{0}\'. Error #{1[0]}: {1[1]}'.format(args[0], sys.exc_info()[1].args))

@require('msg', str)
def quit(msg, status = 0):
	print msg
	sys.exit(status)

@require('method', (FunctionType, BuiltinFunctionType))
@require('message', str)
def catch(method, args, message, exceptions = (OSError, IOError)):
	if not isinstance(args, (list, tuple)):
		args = [args]
	
	return newcatch(message, exceptions, method, *args)

@require('method', (FunctionType, BuiltinFunctionType))
@require('message', str)
def newcatch(message, exceptions, method, *args, **kwargs):
	try:
		return method(*args, **kwargs)
	except exceptions:
		quit(message.format(*args, **kwargs) + '\nError #{0[0]}: {0[1]}'.format(sys.exc_info()[1].args), 1)

def wrap(method, message, exceptions = (OSError, IOError)):
	def fn(*args, **kwargs):
		return newcatch(message, exceptions, method, *args, **kwargs)
	
	return fn

open = wrap(open, 'Can\'t open \'{0}\'')
