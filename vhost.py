#!/usr/bin/python

import os, sys, re
from optparse import OptionParser

parser = OptionParser(usage='%prog SERVERNAME [options]')

argstuple = ({'name': 'server', 'help': 'Apache virtual host name', 'short': 's', 'metavar': 'SERVER'}, {'name': 'docroot', 'help': 'Apache document root.', 'short': 'd', 'default': '/var/www/{0}/public_html/'}, {'name': 'admin', 'help': 'Admin\'s contact email', 'default': 'webmaster@localhost', 'short': 'a'}, {'name': 'override_docroot', 'help': 'AllowOverride for DocumentRoot', 'default': 'All'}, {'name': 'override_cgi', 'help': 'AllowOverride for cgi-bin', 'default': 'All'}, {'name': 'host_template', 'help': 'Template for host configuration (./config.template by default)', 'default': 'config.template'})


"""args = dict((i[0], i[2]) for i in argstuple if len(i) == 3)
req_args = [i[0] for i in argstuple if len(i) < 3]"""

serv = 'apache2'

def safe_open(*args):
	try:
		return open(*args)
	except IOError:
		sys.exit('Error when tried to open file \'{0}\'. Error #{1[0]}: {1[1]}'.format(args[0], sys.exc_info()[1].args))

def help():
	return 

def quit(msg, status = 0):
	print msg
	sys.exit(status)

if __name__ == '__main__':
	for i in argstuple:
		args = ['--'+i.pop('name'), 'short' in i.keys() and '-'+ i.pop('short', '') or '']
		parser.add_option(*args, **i)
	
	(options, arguments)  = parser.parse_args()
	
	if len(arguments) < 1:
		quit(parser.format_help())
	
	args = options.__dict__
	args['server'] = arguments[0]

	with os.popen('whereis ' + serv) as response:
		for x in re.split('\s+', ''.join(response)[len(serv)+1:].strip()):
			if x[0:5] == '/etc/':
				where = x
				break
	
	if len(where) == 0:
		quit('No apache config found')
	
	# check if there's no other same named host
	sites = where + '/sites-enabled'
	hosts = [f for f in os.listdir(sites) if f[-1] != '~']
	for i in hosts:
		with safe_open(sites + '/' + i) as config:
			for l in config:
				if re.split('\s+', l.strip('\t ')) == ['ServerName', args['server']]:
					quit('A host with ServerName \'{0}\' already exists.'.format(args['server']))
		
	
	# need to check if docroot does not exists or is empty
	args['docroot'] = args['docroot'].format(args['server'])
	if os.path.lexists(args['docroot']):
		if not os.path.isdir(args['docroot']):
			quit('docroot was a file or a link (\'{0}\')'.format(args['docroot']), 1)
		try:
			if os.listdir(args['docroot']) != []:
				quit('docroot parameter was a non-empty directory (\'{0}\')'.format(args['docroot']), 1)
		except OSError:
			quit('Document root (\'{0}\') exists and is not accessible.'.format(args['docroot']), 1)
	else:
		try:
			os.makedirs(args['docroot'])
		except OSError:
			quit('Can\'t create document root directory \'{0}\''.format(args['docroot']), 1)
	
	# create apache vhost file
	new_conf = where + '/sites-available/' + args['server']
	
	with safe_open(args['host_template']) as conf_src:
		try:
			with safe_open(new_conf, 'w') as conf_dest:
				for l in conf_src:
					conf_dest.write(l.format(**args))
		except KeyError:
			msg = '\nOops, your template \'{0}\' has placeholders for parameters\nthat were not supplied in the command line:\n - {1}\n'.format(args['host_template'], '\n - '.join(sys.exc_info()[1].args))
			try:
				os.rmdir(args['docroot'])
			except OSError:
				msg += 'Tried to remove document root (\'{0}\'), but failed: error #{1[0]} {1[1]}'.format(args['docroot'], sys.exc_info()[1].args)
			quit(msg, 1)
	
	# link it
	try:
		os.chdir(where + '/sites-enabled')
		os.symlink(new_conf, args['server'])
	except OSError:
		quit('Couldn\'t add host to enabled hosts (make symlink in \'{0}\'). Error #{1[0]}: {1[1]}'.format(where + '/sites-enabled', sys.exc_info()[1].args))
	
	# add to /etc/hosts
	with safe_open('/etc/hosts', 'a') as hosts:
		try:
			hosts.write('\n127.0.0.1\t{0}.{1}'.format(args['server'], os.uname()[1]))
		except IOError:
			quit('Can\'t add host to \'/etc/hosts\'. Error #{0[0]}: {0[1]}.'.format(sys.exc_info()[1].args))
		except OSError:
			quit('Couldn\'t get computer name. Error #{0[0]}: {0[1]}.'.format(sys.exc_info()[1].args))
	
	# restart apache
	command = '/etc/init.d/{0} restart'.format(serv)
	try:
		os.system(command)
	except OSError:
		quit('Couldn\'t restart {0} ({2}). Error #{1[0]}: {1[1]}'.format(serv, command, sys.exc_info()[1].args))

