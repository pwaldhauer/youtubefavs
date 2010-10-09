#!/bin/bash

for i in `find tmp/*`; do 
	chmod 0777 $i && sh $i; 
done
