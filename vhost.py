#!/usr/bin/python

import os, sys, re

if __name__ == '__main__':
	if '--help' in sys.argv:
		print """

		Usage: vhost --host=<hostname> [--docroot=<document root>]

		Parameters:
			--host		Apache virtual host name
			--docroot	Apache document root.

		"""
		sys.exit()
	
	# create apache vhost file
	# link it
	# create docroot
	# add files templates
	# add to /etc/hosts
	# restart apache
	
	serv = 'apache2'
	with os.popen('whereis ' + serv) as response:
		where = [x for x in re.split('\s+', ''.join(response)[len(serv)+1:].strip()) if '/etc/' in x]
	
	if len(where) == 0:
		print "No apache config found"
		sys.exit()
	
	print where
