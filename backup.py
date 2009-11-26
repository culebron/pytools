#!/usr/bin/python

"""Backup script. Usage: """

import os, sys, re, parse
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
	'short': 't'})

volumes = {
	'DVD': 4.7e+9,
	'DVD-DL': 4.7e+9 * 2,
	'CD': 7.0e+8,
	'BR': 2.2e+10
}

graft_name = 'graft-disk-{0}.txt'

if __name__ == '__main__':
	parse.shovel(parse_params)
	
	dirs = [[], []]
	for i in parse.arguments:
		dirs[os.path.isdir(i)].append(i)
	
	if len(dirs[0]) > 0:
		print 'The following paths aren\'t directories and were ignored:'
		print '\n'.join(non_dirs)
	
	dates = {}
	for i in ['from', 'to']:
		match = re.search(r'^(\d{4})-(\d\d)-(\d\d)$', str(parse.options.get(i, '')))
		if match is not None:
			dates[i] = match.groups(0)
	
	dirs = dirs[1]
	command = 'find {0} -type f -readable -daystart '
	for i in dates:
		command += ' -mtime {1}{0}'.format((date(*map(int, dates[i])) - date.today()).days, '+' if i == 'to' else '')
	
	def scanner():
		for p in dirs:
			with os.popen(command.format(p)) as r:
				for l in r:
					yield {'name': l[:-1], 'size': os.path.getsize(l[:-1])}

	disk_capacity = volumes[parse.options['volume']] if parse.options['volume'] not in volumes else volumes['DVD']
	
	file_size = disk_size = 0
	disk_num = 1
	x = scanner()
	y = ''

	try:		
		while y or '' == y:
			with open(graft_name.format(disk_num), 'w') as graft:
				while y or '' == y:
					graft.write('{1}={0}\n'.format(dirpath, os.path.relpath(dirpath, os.getenv('HOME'))))
					y = x.next()
					
					if y['size'] 
	
	except StopIteration:
		pass				
				# записать строку из y
				# проверить размер файла в i

if False:
	paths = sys.argv[1:]
	todel = []
	disk_capacity = 4.7e+9
	for p in paths:
		match = re.search(r'^--from=(\d{4})-(\d\d)-(\d\d)$', p)
		if match is None:
			continue
	
		start_date = match.groups(0)
		todel.append(p)

	for d in todel:
		paths.remove(d)

	command = 'find {0} -type f -readable'
	if 'start_date' in globals():
		command += ' -mtime {0}'.format((date(*map(int, start_date)) - date.today()).days)

	graft_name = 'graft-disk-{0}.txt'
	file_size = 0
	disk_size = 0
	
	while 
	
	for l in scanner():
		dirpath = l.replace('\n', '')
		fsize = os.path.getsize(dirpath)
		if disk_size + fsize > disk_capacity:
			print 'end disk' + str(disk_num)
			graft.close()
			disk_num += 1
			graft = open(graft_name.format(disk_num), 'w')
			disk_size = 0
	
		disk_size += fsize
		graft.write('{1}={0}\n'.format(dirpath, os.path.relpath(dirpath, os.getenv('HOME'))))
	
		#if os.path.isdir(path):
		#	continue
	
		#f.write(path + '=' + path + '\n')
	graft.close()


	#if '--debug' not in paths:
	#	command += ' > grafts'

	graft_name = 'graft-disk-{0}.txt'
	disk_size = 0
	disk_num = 1
	graft = open(graft_name.format(disk_num), 'w')
	for p in paths:
		if not os.path.isdir(p):
			continue
		#print 'command: ' + command.format(p)
		for l in os.popen(command.format(p)):
			#print l,
			dirpath = l.replace('\n', '')
			fsize = os.path.getsize(dirpath)
			#print disk_size, fsize, disk_capacity
			if disk_size + fsize > disk_capacity:
				print 'end disk' + str(disk_num)
				graft.close()
				disk_num += 1
				graft = open(graft_name.format(disk_num), 'w')
				disk_size = 0
		
			disk_size += fsize
			graft.write('{1}={0}\n'.format(dirpath, os.path.relpath(dirpath, os.getenv('HOME'))))
		
			#if os.path.isdir(path):
			#	continue
		
			#f.write(path + '=' + path + '\n')
	graft.close()

	for i in range(1, disk_num + 1):
		isocmd = ('mkisofs -o disk-{0}.iso -rJ -graft-points --path-list '+graft_name).format(i)
	
		os.system(isocmd)
		print isocmd
		#os.unlink(graft_name.format(i))

