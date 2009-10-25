#!/usr/bin/python

import os, sys, re, gtk

options = { \
	'pw': lambda m: [m[0][3]], \
	'rest': lambda m: [y for y in m if y not in args], \
	'all': lambda m: map(' '.join, m)}

def getData(how_much = 'pw', *args):
	if len(args) == 0 or how_much not in options.keys(): # stop if no search args or how_much is wrong
		return False
	result = [x for x in os.popen('cat ~/.py/backuptool/.pw | ' + ' | '.join(map('grep -w \'{0}\''.format, args)))] # run "cat | grep | grep, etc" command
	
	match = [re.search(r'^\s*(\w+)\s+(\w+)\s+(\w+)\s+(\w+)(\s+(\w+)\s*|)$', x).groups(0) for x in result] # search strings in each line
	return options[how_much](match) # run a lambda from options
	
def copy(lines):
	"""Copies a list of text lines.
	lines: list of strings."""
	if not isinstance(lines, (list, tuple)):
		return False
	cli = gtk.Clipboard(gtk.gdk.display_manager_get().get_default_display(), "CLIPBOARD")
	cli.clear()
	cli.set_text('\n'.join(map(str, lines))) # copying to clipboard
	cli.store()

if __name__ == "__main__":
	args = sys.argv[1:]
	for x in args:
		if x[0] == '-' and x[1:] in options.keys():
			how_much = x[1:]
			args.remove(x)
	
	copy(getData('pw', *args))

