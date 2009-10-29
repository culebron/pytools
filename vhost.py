#!/usr/bin/python

import os, sys, re, parse, safe
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

if __name__ == '__main__':
	if os.getenv('USERNAME') != 'root':
		quit('I can\'t do anything this way. Sudo me, please!')

	if len(parse.arguments) < 1: # 1 argument (server name) is required
		quit(parse.parser.format_help())
	
	args = parse.options.__dict__ # take dictionary of options to use as dict
	args['server'] = parse.arguments[0] # these vars are needed to pass to format()
	args['servername'] = parse.arguments[0] + '.' + os.uname()[1] # ...into config.template
	
	where = ''
	with os.popen('whereis ' + serv) as response: # searching for apache in /etc/
		for x in re.split('\s+', ''.join(response)[len(serv)+1:].strip()): # join lines if >1, split by spaces
			if x[0:5] == '/etc/':
				where = x
				break # first /etc/ is what we looked for
	
	if where == '':
		quit('Apache config directory not found in /etc/')
	
	# check if there's no other same named host
	sites = os.path.join(where, 'sites-enabled')
	for h in (f for f in safe.catch(os.listdir, sites, 'Can\'t list {0} directory') if f[-1] != '~'): # open all files except backup versions '*~'.
		with safe.fopen(os.path.join(sites, h)) as config: # scan configs. need to close automatically afterwards
			for l in config: # scan config
				l2 = l.strip('\t ') # strip spaces and tabs
				if re.split('\s+', l2) == ['ServerName', args['servername']]: # if it's not commented (commented produces ['#', ...] list)
					quit('A host with ServerName \'{0}\' already exists.'.format(args['servername']))

	# need to check if docroot does not exists or is empty
	args['docroot'] = args['docroot'].format(args['server']) # by default docroot is named as /var/www/host/public_html
	if os.path.lexists(args['docroot']):
		if not os.path.isdir(args['docroot']):
			quit('docroot was a file or a link (\'{0}\')'.format(args['docroot']), 1)
		if safe.catch(os.listdir, args['docroot'], 'Document root (\'{0}\') exists but is not accessible.') != []: # try to list the directory. may fail if no access rights
			quit('docroot parameter was a non-empty directory (\'{0}\')'.format(args['docroot']), 1)
	else:
		safe.catch(os.makedirs, args['docroot'], 'Can\'t create document root directory \'{0}\'')
	
	safe.catch(os.chown, (args['docroot'], int(os.getenv('SUDO_UID')), int(os.getenv('SUDO_GID'))), 'Can\'t change document root ownership \'{0}\'')
	

	# create apache vhost file
	new_conf = os.path.join(where, 'sites-available', args['server'])
	
	with safe.fopen(args['host_template']) as conf_src:
		try:
			with safe.fopen(new_conf, 'w') as conf_dest:
				for l in conf_src:
					conf_dest.write(l.format(**args))
		except KeyError:
			msg = '\nOops, your template \'{0}\' has placeholders for parameters\nthat were not supplied in the command line:\n - {1}\n'.format(args['host_template'], '\n - '.join(sys.exc_info()[1].args))
			safe.catch(os.rmdir, args['docroot'], msg + 'Couldn\'t remove document root (\'{0}\')')
			quit(msg, 1)
	
	# link it
	couldnt_add_host = 'Couldn\'t add host to enabled hosts (make symlink in \'{0}\')'
	safe.catch(os.chdir, where + '/sites-enabled', couldnt_add_host)
	safe.catch(os.symlink, [new_conf, args['server']], couldnt_add_host)
	
	# add to /etc/hosts
	with safe.fopen('/etc/hosts', 'a') as hosts:
		safe.catch(hosts.write, '\n127.0.0.1\t{0}'.format(args['servername']), 'Can\'t add host to \'/etc/hosts\'.')
	
	# restart apache
	command = '/etc/init.d/{0} restart'.format(serv)
	safe.catch(os.system, command, 'Couldn\'t restart ' + serv + '({0})')

