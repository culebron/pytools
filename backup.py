#!/usr/bin/python

"""Backup script. Usage: """

import os, sys, re, parse, math
from datetime import date

parse.parser.usage = '%prog [paths] [options]'
parse_params = ({'name': 'exclude',
	'help': 'Exclude files and folders by wildcard.',
	'short': 'x',
	'action': 'append'},
{	'name': 'include',
	'help': 'Include by wildcard.',
	'short': 'i',
	'action': 'append'},
{	'name': 'volume',
	'help': 'How big should volumes be. (CD, DVD, DVD-DL = double-layer, BR = blue ray, 0 if no limit)',
	'default': 'DVD'},
{	'name': 'from',
	'help': 'Find files modified not more than that day.',
	'short': 'f'},
{	'name': 'to',
	'help': 'Modified in this day or earlier.',
	'short': 't'},
{	'name': 'output',
	'help': 'Where to put ISO files',
	'short': 'o',
	'default': '/tmp'},
{	'name': 'debug',
	'short': 'd',
	'help': 'Debugging mode (don\'t create ISO images)',
	'action': 'store_true',
	'default': True})

volumes = {
	'DVD': 4.7e+9,
	'DVD-DL': 4.7e+9 * 2,
	'CD': 7.0e+8,
	'BR': 2.2e+10,
	'test': 1e+8
}

disk_overhead = 0.95
graft_name = '/tmp/graft-disk-{0}.txt'

def padn(n, b = 2048):
	return n - (n % b) + b if n % b > 0 else n

if __name__ == '__main__':
	parse.shovel(parse_params)
	
	dirs = [[], []]
	for i in parse.arguments:
		dirs[os.path.isdir(i)].append(i)
	
	if len(dirs[0]) > 0:
		print 'The following paths aren\'t directories and were ignored:\n' + '\n'.join(non_dirs)
	
	dates = {}
	for i in ['from', 'to']:
		match = re.search(r'^(\d{4})-(\d\d)-(\d\d)$', str(parse.options.get(i, '')))
		if match is not None:
			dates[i] = match.groups(0)

	def removeAncestors(dirs):
		for i in dirs:
			for j in dirs:
				if i == j:
					continue
				if os.path.commonprefix([i, j]) == j:
					dirs.remove(i)
	
	dirs = list(set(dirs[1]))
	removeAncestors(dirs)
	
	command = 'find {0} -type f -readable -daystart '

	def joiner(join, fmt, array):
		if not parse.options[array]:
			return ''
		
		return join.join([fmt.format(i) for i in parse.options[array]])
	
	command += joiner(' ', ' -not -path "{0}" ', 'exclude') + ('{0}' if len(parse.options['include']) < 2
		else '\( {0} \)').format(joiner(' -or ', '-path "{0}"', 'include'))

	for i in dates:
		command += ' -mtime {1}{0}'.format((date(*map(int, dates[i])) - date.today()).days, '+' if i == 'to' else '')
	
	disk_capacity = volumes[parse.options['volume']] if parse.options['volume'] in volumes else volumes['DVD'] # ugly
	#disk_capacity = int(disk_capacity * disk_overhead)
	disk_num = 0
	
	def scanner(): # to iterate over several finds like over one
		for p in dirs:
			with os.popen(command.format(p)) as r:
				if parse.options['debug']:
					print command.format(p)
				for l in r:
					yield {'name': l[:-1], 'size': padn(os.path.getsize(l[:-1]))}
	
	x = scanner() # to use it both in and out of the DISK loop. Otherwise I'd just did 'for i in scanner()'.

	try: # a way to break the DISK LOOP (x.next() will raise StopIteration)
		y = x.next()
		while True: # infinite DISK LOOP
			disk_num += 1
			with open(graft_name.format(disk_num), 'w') as graft:
				disk_size = 0
				while y or '' == y: # MAIN LOOP
					if y is not '': # loop to skip the initial pass (y='')
						graft.write('{1}={0}\n'.format(y['name'],
						os.path.relpath(y['name'], os.getenv('HOME')))) # writing the path to file and path relative to home dir
						disk_size += y['size']
					
					while True: # loop to skip files larger than the media
						y = x.next() # getting the next element
						if y['size'] < disk_capacity:
							break
						print 'File {0} was omitted since it\'s greater than size of {1}'.format(y['name'], parse.options['volume'])
					
					if disk_size + y['size'] > disk_capacity:
						break # breaks the MAIN LOOP and goes to the next disk.
	
	except StopIteration:
		pass # list is over, that's it

	iso_path = 'disk-{0}.iso'
	for i in range(1, disk_num + 1):
		isocmd = ('mkisofs -o {1} -rJU -iso-level 3 -graft-points --path-list {0}').format(graft_name.format(i), os.path.join(parse.options['output'], iso_path.format(i)))
	
		print 'Executing command:'
		print isocmd
		#os.system(isocmd)

