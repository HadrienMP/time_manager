#!/bin/sh
find . -iname "*.php" | xargs chmod 755
path=${PWD}
python deploy.py $path
echo ""
echo "!!!ATTENTION!!!"
echo "Ne pas oublier de modifier les paramètres tank_auth pour correspondre au serveur et au mode"