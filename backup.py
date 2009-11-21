#!/usr/bin/python

"""Backup script. Usage: """

import os, sys, re, parse
from datetime import date

parse.parser.usage = '%prog [paths] [options]'
parse_params = ({'name': 'exclude-name',
	'help': 'Exclude files by filename wildcard.',
	'short': 'n',
	'default': None,
	'action': 'append'},
{'name': 'exclude-path',
	'help': 'Exclude files by path wildcard.',
	'default': None,
	'short': 'p'},
{'name': 'volume',
	'help': 'How big should volumes be. (CD, DVD, DVD-DL = double-layer, BR = blue ray, 0 if no limit)',
 'default': 'DVD'})

volumes = {
	'DVD': 4.7e+9,
	'DVD-DL': 4.7e+9 * 2,
	'CD': 7.0e+8,
	'BR': 2.2e+10
}

if __name__ == '__main__':
	parse.shovel(parse_params)
	pass

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

