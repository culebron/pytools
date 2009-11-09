#!/usr/bin/python

import os, parse, safe, apachehost

parse.parser.usage = '%prog SERVERNAME [options]'
parse.shovel({'name': 'docroot', 'help': 'Server\'s document root. Default: /var/www/SERVERNAME/public_html', 'short': 'd', 'default': '/var/www/{0}/public_html/'},\
{'name': 'admin', 'help': 'Admin\'s contact email', 'default': 'webmaster@localhost', 'short': 'a'},\
{'name': 'override_docroot', 'help': 'AllowOverride for DocumentRoot', 'default': 'All'},\
{'name': 'override_cgi', 'help': 'AllowOverride for cgi-bin', 'default': 'All'},\
{'name': 'host_template', 'help': 'Template for host configuration (./config.template by default)', 'default': os.path.join(os.path.dirname(os.path.realpath(__file__)), 'templates', 'config.template')})


if __name__ == '__main__':
	if len(parse.arguments) < 1: # 1 argument (server name) is required
		safe.quit(parse.parser.format_help())
	
	apachehost.create(parse.options, parse.arguments)
