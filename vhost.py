#!/usr/bin/python

import os, sys, re, argsdict

args = {'admin': 'webmaster@localhost', 'override-docroot': 'All', 'override-cgi': 'All', 'host-template': 'config.template'}
req_args = ['host', 'docroot']
serv = 'apache2'

def help():
	return """
	Usage: vhost --host=<hostname> [--docroot=<document root>]

	Parameters:
		--host			Apache virtual host name
		--docroot		Apache document root.
		--admin			Admin's contact email
		--override-docroot	AllowOverride for DocumentRoot
		--override-cgi		AllowOverride for cgi-bin
		--host-template		Template for host configuration (./config.template by default)
		"""

def quit(msg):
	print msg
	sys.exit()

if __name__ == '__main__':
	args.update(argsdict.args(args.keys() + req_args))
	
	if '--help' in sys.argv or not set(req_args).issubset(set(args.keys())): # required arguments must be supplied in CL
		quit(help())
	
	with os.popen('whereis ' + serv) as response:
		where = [x for x in re.split('\s+', ''.join(response)[len(serv)+1:].strip()) if '/etc/' in x]
	
	if len(where) == 0:
		quit('No apache config found')
	
	# need to check if docroot not exists or is empty
	if os.path.lexists(args['docroot']):
		if not os.path.isdir(args['docroot']):
			quit('docroot was a file or a link (\'{0}\')'.format(args['docroot']))
		if os.listdir(args['docroot']) != []:
			quit('docroot parameter was a non-empty directory (\'{0}\')'.format(args['docroot']))
	else:
		# try to create directory
		
	
	# check if there's no other same named host
	
	
	# create apache vhost file
	try:
		with open(args['host-template']) as conf_file:
			vhost_cfg = ''.join(conf_file).format(**args)
	except IOError:
		quit('Can\'t open file \'{0}\'. Error #{1[0]}: {1[1]}. {2}'.format(args['host-template'], sys.exc_info()[1].args, (sys.exc_info()[1][0] == 13 and 'Check file access permissions.' or '')))
	except KeyError:
		quit('\nOops, your template \'{0}\' has placeholders for parameters\nthat were not supplied in the command line:\n - {1}\n\nCan\'t proceed. Ending. Nothing has been changed yet.'.format(args['host-template'], '\n - '.join(sys.exc_info()[1].args)))
	
	print vhost_cfg

	# link it
	# create docroot
	# add files templates
	# add to /etc/hosts
	# restart apache
	
