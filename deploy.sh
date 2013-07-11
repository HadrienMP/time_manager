#!/bin/sh
find . -iname "*.php" | xargs chmod 755
path=${PWD}
python deploy.py $path