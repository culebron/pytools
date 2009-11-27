#!/usr/bin/python

import os, sys, re, shutil
#from datetime import datetime # need only datetime format

def freespace(p):
	"""
	Returns the number of free bytes on the drive that ``p`` is on
	"""
	s = os.statvfs(p)
	return s.f_bsize * s.f_bavail

defdate = (1,)*6
stdate = defdate # default start date is current eon's first day
dirs = [] # paths supplied in arguments
for p in sys.argv:
	if os.path.isdir(p):
		dirs.append(p) # if an argument is directory, add it to scanned dirs
		continue
	
	mobj = re.match(r'^--from=(\d{4})-(\d\d)-(\d\d)', p) # otherwise check if it is start date
	if mobj:
		stdate = mobj.groups(0) # write matched strings tuple into stdate

stdate = map(int, stdate) # convert into int and into datetime

if len(dirs) < 2: # if less than 2 directories are supplied, terminate
	print 'Not enough parameters supplied.'
	sys.exit()

dirfrom, dirto = dirs[0:2]
del dirs
files = []
totalsize = 0
freesize = freespace(dirto)

for f in os.popen('find '+ dirfrom + ' -iname \'*.mp3\' -type f -exec sha1sum {} \\;'): # run find command for mp3 files in the source directory
	#fpath = os.path.join(dirfrom, f.replace('\n', '')) # make a full path to the file
	mobj = re.match(r'([\da-f]{40})  (.*)\n', f)
	if not mobj:
		continue
	fhash, fpath = mobj.groups(0)
	fdate = defdate # file's date is 1-1-1 by default
	
	mobj = re.search(r'(\d{4})\D(\d\d)\D(\d\d)\D(\d\d)\D(\d\d)\D(\d\d)\.mp3$', fpath, re.I) # if it is in date format, then add date field (works both if file was d-m-y, and y-m-d)
	if mobj:
		fdate = mobj.groups(0); # change file's date
	del mobj
	
	fdate = map(int, fdate) # convert it into datetime
	if fdate >= stdate: # if date is more than start date, then copy it (if file has no date, it's default, and will also be copied if start date is also default)
		files.append({'path': fpath, 'size': os.path.getsize(fpath), 'date': fdate, 'hash': fhash}) # add to array
	

files.sort(key = lambda d: d.get('date'), reverse = True) # sort it by date, key is date 

targetfiles = {}
for f in os.popen('find '+ dirto + ' -iname \'*.mp3\' -type f -exec sha1sum {} \\;'):
	mobj = re.match(r'([\da-f]{40})  (.*)\n', f)
	if not mobj:
		continue
	targetfiles[mobj.groups(0)[0]] = mobj.groups(0)[1]

"""for f in files:
	if f['hash'] in targetfiles.keys():
		targetfiles[f['hash']]"""

cpfiles = []
warn = False
for f in files:
	if totalsize + f['size'] <= freesize:
		cpfiles.append(f)
		totalsize += f['size']

if len(files) > len(cpfiles):
	print 'WARNING. Only {0} files ({1} bytes) can be copied, because there\'s only {2} bytes of free space on target.'.format(len(cpfiles), totalsize, freesize)

cpfiles.sort(key = lambda d: d.get('date'))
del files
copysize = 0.0
typed = 0

print '{0} bytes to copy'.format(totalsize)
for f in cpfiles:
	print 'Copying: ' + os.path.split(f['path'])[-1],
	
	if re.search(r'(\d\d\D){2}\d{4}(\D\d\d){3}\.mp3$', fpath, re.I): # if it has date in string, in reversed format, rename it
		cre = re.compile('(\d\d)\D(\d\d)\D(\d{4})\D(\d\d)\D(\d\d)\D(\d\d)\\.mp3$', re.I) # rename it to a new format
		fnewpath = re.sub(cre, '\\3-\\2-\\1 \\4-\\5-\\6.mp3', fpath) # new path (year in the beginning)
		#shutil.move(fpath, fnewpath) # renaming
		#fpath = fnewpath # replacing file path
		#del cre, fnewpath
	
	shutil.copy2(f['path'], dirto) # copy the files to target directory
	copysize += f['size']
	print  'Done. {0:.0%} ready. '.format(copysize / totalsize)

#print # '\b' * typed + 'Done'
