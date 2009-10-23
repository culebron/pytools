#!/usr/bin/python
import sys, re
"""Command line parameters extracter"""

def getParamsDict(*args):
	"""arguments: a string of space-separated parameters, a list, or a number of arguments.
	Returns a dict of parameter names and values.
	Example: a script called with parameters: --host=lor_sam_pau --user=fei --pass=stars\ are\ bright\ tonight --db=pirate_bay
	> argsdict.getParamsDict('user pass')
	{'user': 'fei', 'pass': 'stars are bright tonight'}"""
	if len(args) == 1:
		if isinstance(args[0], str):
			args = re.split('\s+', args[0].strip()) # if it's one string, let's split it by spaces
	
	pars = dict((p, '--'+p+'=') for p in map(str, args)) # to use pairs: (v, --v=) for convenience.
	
	return dict((p, a[len(pars[p]):]) for p in pars for a in sys.argv[1:] if a[0:len(pars[p])] == pars[p])


