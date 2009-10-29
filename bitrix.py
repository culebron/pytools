#!/usr/bin/python

import os, sys, shutil
from optparse import OptionParser

parser = OptionParser('%prog HOST')

bitrix_file = 'bitrix8setup.php'
argstuple = ({'name': 'installer', 'help': 'Path to bitrix installer script', 'short': 'd', 'default': os.path.dirname(os.path.realpath(__file__)) + '/' + bitrix_file})



if __name__ == '__main__':
	#shutil.copy()
	pass
