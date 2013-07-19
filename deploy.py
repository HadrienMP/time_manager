#!/usr/bin/python
# -*- coding: UTF-8 -*-
# Filename: deploy.py

import sys
import re

if len(sys.argv) != 2:
    print "Ce script a besoin du chemin codeigniter en paramètre"
    sys.exit()

print "Analyse des paramètres"
path = sys.argv[1]
print "    - Chemin trouvé : " + path
path = re.sub(r'/cygdrive/(\w)/', r"\1:/", path)
path = path[0].upper() + path[1:]
print "    - Chemin modifié : " + path

# Lecture du fichier
print "\nOuverture du fichier htdocs/index.php pour modification"
path_file = 'htdocs/index.php'
f = open(path_file,'r')
index_php = f.read()
f.close()

index_php = re.sub(r"(application_folder|system_path) = '.*(application|system)'", r"\1 = '" + path + r"/\2'", index_php)

# Réécriture dans le fichier
f = open(path_file,'w')
f.write(index_php)
f.close()

print "Fin de la modification du fichier htdocs/index.php"



# Lecture du fichier
print "\nOuverture du fichier conf/time_manager.conf pour modification"
path_conf_file = 'conf/time_manager.conf'
f = open(path_conf_file,'r')
conf_file = f.read()
f.close()

conf_file = re.sub(r"\".*/htdocs", "\"" + path + r"/htdocs", conf_file)

# Réécriture dans le fichier
f = open(path_conf_file,'w')
f.write(conf_file)
f.close()

print "Fin de la modification du fichier conf/time_manager.conf"