#!/usr/bin/python

import os, sys, re, argsdict

from optparse import OptionParser
parser = OptionParser()


argstuple = (('server', 'Apache virtual host name'), ('docroot', 'Apache document root.'), ('admin','Admin\'s contact email', 'webmaster@localhost'), ('override-docroot', 'AllowOverride for DocumentRoot', 'All'), ('override-cgi', 'AllowOverride for cgi-bin', 'All'), ('host-template', 'Template for host configuration (./config.template by default)', 'config.template'))

args = dict((i[0], i[2]) for i in argstuple if len(i) == 3)
req_args = [i[0] for i in argstuple if len(i) < 3]

serv = 'apache2'



def help():
	return 

def quit(msg, status = 0):
	print msg
	sys.exit(status)

if __name__ == '__main__':
	args.update(argsdict.args(args.keys() + req_args))
	
	if '--help' in sys.argv or not set(req_args).issubset(set(args.keys())): # required arguments must be supplied in CL
		quit(help())
	
	with os.popen('whereis ' + serv) as response:
		where = [x for x in re.split('\s+', ''.join(response)[len(serv)+1:].strip()) if '/etc/' in x]
	
	if len(where) == 0:
		quit('No apache config found')

	# check if there's no other same named host
	hosts = [f for i in where for f in os.listdir(i + '/sites-enabled') if f[-1] != '~']
	for i in hosts:
		try:
			with open(i) as config:
				for l in config:
					if re.split('\s+', l.strip('\t ')) == ['ServerName', args['host']]:
						quit()
		except IOError:
			
	
	# need to check if docroot does not exists or is empty
	if os.path.lexists(args['docroot']):
		if not os.path.isdir(args['docroot']):
			quit('docroot was a file or a link (\'{0}\')'.format(args['docroot']), 1)
		if os.listdir(args['docroot']) != []:
			quit('docroot parameter was a non-empty directory (\'{0}\')'.format(args['docroot']), 1)
	
	
	# create apache vhost file
	try:
		with open(args['host-template']) as conf_file:
			vhost_cfg = ''.join(conf_file).format(**args)
	except IOError:
		quit('Can\'t open file \'{0}\'. Error #{1[0]}: {1[1]}. {2}'.format(args['host-template'], sys.exc_info()[1].args, (sys.exc_info()[1][0] == 13 and 'Check file access permissions.' or '')), 1)
	except KeyError:
		quit('\nOops, your template \'{0}\' has placeholders for parameters\nthat were not supplied in the command line:\n - {1}\n\nCan\'t proceed. Ending. Nothing has been changed yet.'.format(args['host-template'], '\n - '.join(sys.exc_info()[1].args)), 1)
	
	print vhost_cfg

	# link it
	# create docroot
	# add files templates
	# add to /etc/hosts
	# restart apache
	
