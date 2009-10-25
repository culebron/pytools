#!/usr/bin/python

"""Backup script. Usage: """

import os, sys, re
from datetime import date

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
#	command += ' > grefs'

gref_name = 'gref-disk-{0}.txt'
disk_size = 0
disk_num = 1
gref = open(gref_name.format(disk_num), 'w')
for p in paths:
	if not os.path.isdir(p):
		continue
	#print 'command: ' + command.format(p)
	for l in os.popen(command.format(p)):
		#print l,
		dirpath = os.path.join(p, l.replace('\n', ''))
		fsize = os.path.getsize(dirpath)
		#print disk_size, fsize, disk_capacity
		if disk_size + fsize > disk_capacity:
			print 'end disk' + str(disk_num)
			gref.close()
			disk_num += 1
			gref = open(gref_name.format(disk_num), 'w')
			disk_size = 0
		
		disk_size += fsize
		gref.write('{0}={0}\n'.format(dirpath))
		
		#if os.path.isdir(path):
		#	continue
		
		#f.write(path + '=' + path + '\n')
gref.close()

for i in range(1, disk_num + 1):
	isocmd = ('mkisofs -o disk-{0}.iso -rJ --path-list '+gref_name).format(i)
	
	#os.system(isocmd)
	print isocmd
	#os.unlink(gref_name.format(i))

