#!/usr/bin/python

import os, sys, re, parse, safe
from contextlib import nested
parse.parser.usage = '%prog SERVERNAME [options]'
parse.shovel({'name': 'docroot', 'help': 'Server\'s document root. Default: /var/www/SERVERNAME/public_html', 'short': 'd', 'default': '/var/www/{0}/public_html/'},\
{'name': 'admin', 'help': 'Admin\'s contact email', 'default': 'webmaster@localhost', 'short': 'a'},\
{'name': 'override_docroot', 'help': 'AllowOverride for DocumentRoot', 'default': 'All'},\
{'name': 'override_cgi', 'help': 'AllowOverride for cgi-bin', 'default': 'All'},\
{'name': 'host_template', 'help': 'Template for host configuration (./config.template by default)', 'default': 'config.template'})

serv = 'apache2'

def quit(msg, status = 0):
	print msg
	sys.exit(status)

def find_server(where, servername):
	sites = os.path.join(where, 'sites-enabled')
	for h in (f for f in safe.catch(os.listdir, sites, 'Can\'t list {0} directory') if f[-1] != '~'): # open all files except backup versions '*~'.
		sitefile = os.path.join(sites, h)
		with safe.fopen(sitefile) as config: # scan configs. need to close automatically afterwards
			for l in config: # scan config
				l2 = l.strip('\t ') # strip spaces and tabs
				if re.split('\s+', l2) == ['ServerName', servername]: # if it's not commented (commented produces ['#', ...] list)
					yield sitefile
					break

def make_vhost(opts, arguments):
	if os.getenv('USERNAME') != 'root':
		quit('I can\'t do anything this way. Sudo me, please!')

	opts['server'] = arguments[0] # these vars are needed to pass to format()
	opts['servername'] = arguments[0] + '.' + os.uname()[1] # ...into config.template
	
	where = ''
	with os.popen('whereis ' + serv) as response: # searching for apache in /etc/
		for x in re.split('\s+', ''.join(response)[len(serv)+1:].strip()): # join lines if >1, split by spaces
			if x[0:5] == '/etc/':
				where = x
				break # first /etc/ is what we looked for
	
	if where == '':
		quit('Apache config directory not found in /etc/')
	
	# check if there's no other same named host
	try:
		find_server(where, opts['servername']).next() # need to check if there is ANY file with the same servername
		quit('A host with ServerName \'{0}\' already exists.'.format(opts['servername']))
	except StopIteration: # the only way not to consume the generator (find_server) is to get .next() and  catch the exception
		pass
	
	# need to check if docroot does not exists or is empty
	opts['docroot'] = opts['docroot'].format(opts['server']) # by default docroot is named as /var/www/host/public_html
	if os.path.lexists(opts['docroot']):
		if not os.path.isdir(opts['docroot']):
			quit('docroot was a file or a link (\'{0}\')'.format(opts['docroot']), 1)
		if safe.catch(os.listdir, opts['docroot'], 'Document root (\'{0}\') exists but is not accessible.') != []: # try to list the directory. may fail if no access rights
			quit('docroot parameter was a non-empty directory (\'{0}\')'.format(opts['docroot']), 1)
	else:
		safe.catch(os.makedirs, opts['docroot'], 'Can\'t create document root directory \'{0}\'')
	
	safe.catch(os.chown, (opts['docroot'], int(os.getenv('SUDO_UID')), int(os.getenv('SUDO_GID'))), 'Can\'t change document root ownership \'{0}\'')
	

	# create apache vhost file
	new_conf = os.path.join(where, 'sites-available', opts['server'])
	
	try:
		with nested(safe.fopen(opts['host_template']), safe.fopen(new_conf, 'w')) as (conf_src, conf_dest):
			for l in conf_src:
				conf_dest.write(l.format(**opts))
	except KeyError:
		msg = '\nOops, your template \'{0}\' has placeholders for parameters\nthat were not supplied in the command line:\n - {1}\n'.format(opts['host_template'], '\n - '.join(sys.exc_info()[1].args))
		safe.catch(os.rmdir, opts['docroot'], msg + 'Couldn\'t remove document root (\'{0}\')')
		quit(msg, 1)
	
	# link it
	couldnt_add_host = 'Couldn\'t add host to enabled hosts (make symlink in \'{0}\')'
	safe.catch(os.chdir, where + '/sites-enabled', couldnt_add_host)
	safe.catch(os.symlink, [new_conf, opts['server']], couldnt_add_host)
	
	# add to /etc/hosts
	with safe.fopen('/etc/hosts', 'a') as hosts:
		safe.catch(hosts.write, '\n127.0.0.1\t{0}'.format(opts['servername']), 'Can\'t add host to \'/etc/hosts\'.')
	
	# restart apache
	command = '/etc/init.d/{0} restart'.format(serv)
	safe.catch(os.system, command, 'Couldn\'t restart ' + serv + '({0})')

if __name__ == '__main__':
	if len(parse.arguments) < 1: # 1 argument (server name) is required
		quit(parse.parser.format_help())
	
	make_vhost(parse.options.__dict__, parse.arguments)
