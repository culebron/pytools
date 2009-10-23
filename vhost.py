#!/usr/bin/python

import os, sys, re, argsdict
args = {'admin': 'webmaster@localhost', 'override-docroot': 'All', 'override-cgi': 'All', 'host-template': 'config.template'}
add_args = ['host', 'docroot']
if __name__ == '__main__':
	if '--help' in sys.argv:
		print """

		Usage: vhost --host=<hostname> [--docroot=<document root>]

		Parameters:
			--host			Apache virtual host name
			--docroot		Apache document root.
			--admin			Admin's contact email
			--override-docroot	AllowOverride for DocumentRoot
			--override-cgi		AllowOverride for cgi-bin
			--host-template		Template for host configuration (./config.template by default)
		"""
		sys.exit()
	
	# link it
	# create docroot
	# add files templates
	# add to /etc/hosts
	# restart apache
	args.update(argsdict.args(args.keys() + add_args))
	
	serv = 'apache2'
	with os.popen('whereis ' + serv) as response:
		where = [x for x in re.split('\s+', ''.join(response)[len(serv)+1:].strip()) if '/etc/' in x]
	
	if len(where) == 0:
		print "No apache config found"
		sys.exit()
	
	# create apache vhost file
	try:
		with open(args['host-template']) as conf_file:
			vhost_cfg = ''.join(conf_file).format(**args)
	except IOError:
		print 'Can\'t open file \'{0}\'. Error #{1[0]}: {1[1]}.'.format(args['host-template'], sys.exc_info()[1].args)
		if sys.exc_info()[1][0] == 13:
			print 'Check file access permissions.'
		
		sys.exit()
	except KeyError:
		print '\nOops, your template \'{0}\' has placeholders for parameters\nthat were not supplied in the command line:\n - {1}\n\nCan\'t proceed. Ending. Nothing has been changed yet.'.format(args['host-template'], '\n - '.join(sys.exc_info()[1].args))
		sys.exit()
	
	print vhost_cfg
	
