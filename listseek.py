#!/usr/bin/python

"""List searcher."""

def seek(lst, substr = ''):
	"""Searches substr in lst, returns the elements of lst that matched. If no element contains substring, returns an empty list.
	If lst is string, returns lst itself if substr is found, or an empty list otherwise.
	If lst is of other type, attempts to convert it to string and search it."""
	if isinstance(lst, (list, tuple)):
		return [x for x in lst if substr in x]
	
	if isinstance(lst, str):
		if substr in lst:
			return lst
		
		return []
	
	if substr in str(lst):
		return lst
	
	return []

