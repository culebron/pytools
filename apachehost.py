#!/usr/bin/python

import sys, os, safe, re
from contextlib import nested

serv = 'apache2'



@safe.require('l', str)
def parse_line(l):
	return re.split('\s+', l.strip('\t '))



@safe.require('where', str)
@safe.require('servername', str)
def find(where, servername):
	sites = os.path.join(where, 'sites-enabled')
	for h in (f for f in safe.catch(os.listdir, sites, 'Can\'t list {0} directory') if f[-1] != '~'): # open all files except backup versions '*~'.
		sitefile = os.path.join(sites, h)
		with safe.fopen(sitefile) as config: # scan configs. need to close automatically afterwards
			for l in config: # scan config
				if parse_line(l)[0:2] == ['ServerName', servername]: # if it's not commented (commented produces ['#', ...] list)
					yield sitefile
					break



@safe.require('host', str)
@safe.require('params', str, list, tuple)
def get(host, params):
	"""Gets dictionary of requested `param`eters from `host` configuration file.
	Arguments:
	"""
	if not isinstance(params, (list, tuple)):
		params = [params]
	
	with safe.fopen(host) as cfg:
		reader = (parse_line(i)[0:2] for i in cfg)
		return dict([l for l in reader if l[0] in params])



def config_dir():
	with os.popen('whereis ' + serv) as output: # searching for apache in /etc/
		for x in re.split('\s+', ''.join(output)[len(serv)+1:].strip()): # join lines if >1, split by spaces
			if x[0:5] == '/etc/':
				return x
	return None



def create(opts, arguments):
	"""Creates a name-based virtual host (config file), enables it and adds to /etc/hosts."""
	if os.getenv('USERNAME') != 'root':
		safe.quit('I can\'t do anything this way. Sudo me, please!')

	opts['server'] = arguments[0] # these vars are needed to pass to format()
	opts['servername'] = arguments[0] + '.' + os.uname()[1] # ...into config.template
	
	where = config_dir()
	if where is None:
		safe.quit('Apache config directory not found in /etc/')
	
	# check if there's no other same named host
	try:
		find(where, opts['servername']).next() # need to check if there is ANY file with the same servername
		safe.quit('A host with ServerName \'{0}\' already exists.'.format(opts['servername']))
	except StopIteration: # the only way not to consume the generator (find) is to get .next() and  catch the exception
		pass
	
	# need to check if docroot does not exists or is empty
	opts['docroot'] = opts['docroot'].format(opts['server']) # by default docroot is named as /var/www/host/public_html
	if os.path.lexists(opts['docroot']):
		if not os.path.isdir(opts['docroot']):
			safe.quit('docroot was a file or a link (\'{0}\')'.format(opts['docroot']), 1)
		if safe.catch(os.listdir, opts['docroot'], 'Document root (\'{0}\') exists but is not accessible.') != []: # try to list the directory. may fail if no access rights
			safe.quit('docroot parameter was a non-empty directory (\'{0}\')'.format(opts['docroot']), 1)
	else:
		l = ['/']
		for i in os.path.normpath(os.path.abspath(opts['docroot'])).split('/')[1:]:
			l.append(i)
			p = os.path.join(*l)
			if not os.path.isdir(p):
				safe.catch(os.mkdir, p, 'Can\'t create document root directory \'{0}\'')
				safe.catch(os.chown, (p, int(os.getenv('SUDO_UID')), int(os.getenv('SUDO_GID'))), 'Can\'t change document root ownership \'{0}\'')
		del l, p

	# create apache vhost file
	new_conf = os.path.join(where, 'sites-available', opts['server'])
	
	try:
		with nested(safe.fopen(opts['host_template']), safe.fopen(new_conf, 'w')) as (conf_src, conf_dest):
			for l in conf_src:
				conf_dest.write(l.format(**opts))
	except KeyError:
		msg = '\nOops, your template \'{0}\' has placeholders for parameters\nthat were not supplied in the command line:\n - {1}\n'.format(opts['host_template'], '\n - '.join(sys.exc_info()[1].args))
		safe.catch(os.rmdir, opts['docroot'], msg + 'Couldn\'t remove document root (\'{0}\')')
		safe.quit(msg, 1)
	
	print 'Host config saved in {0}. '.format(new_conf),
	
	# link it
	couldnt_add_host = 'Couldn\'t add host to enabled hosts (make symlink in \'{0}\')'
	safe.catch(os.chdir, where + '/sites-enabled', couldnt_add_host)
	safe.catch(os.symlink, [new_conf, opts['server']], couldnt_add_host)
	
	print 'Enabled. ',
	
	# add to /etc/hosts
	with safe.fopen('/etc/hosts', 'a') as hosts:
		safe.catch(hosts.write, '\n127.0.0.1\t{0}'.format(opts['servername']), 'Can\'t add host to \'/etc/hosts\'.')
	
	print 'Added to /etc/hosts. ',
	
	# restart apache
	safe.catch(os.system, '/etc/init.d/{0} restart'.format(serv), 'Couldn\'t restart ' + serv + '({0})')

	print 'Apache restarted successfully. Host {0} is now available at http://{1}.'.format(opts['server'], opts['servername'])

