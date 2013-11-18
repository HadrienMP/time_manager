time_manager
============

Ce site web vous permet de suivre votre temps de travail.
This website holds a tool to allow you to follow their worktime. 

Stats
-----

Grâce à une série de check in et check out, time_manager calcule plusieurs informations : 
* Le temps passé aujourd'hui
* Le temps restant aujourd'hui
* L'heure de fin estimée
* Le nombre d'heures supplémentaires :
 * Aujourd'hui
 * Cette semaine
 * Ce mois
* Le nombre moyen d'heures travaillées :
 *  Par jour
 *  Par semaine

Through a serie of check in and check out, the website is able to calculate the following informations : 
* How long you've worked today 
* How long you still have to work today 
* The estimated time you'll get out of work 
* How much overtime you've worked for :
  * The day
  * The week
  * The month
* The average number of hours you work :
  * Per day 
  * Per week

Export
------

Every month (or any time you want), you'll be able to export all you checks and stats to a CSV (Excel) file. The website will still keep track of your overtime after the export.

Anonymity
---------

* The only thing needed to sign in is a mail address. 
* Your password will be hashed for greater security. 
* The data still is in plain text in the database, but it will be encrypted in the future. 
* Once you export your data, every check is deleted from the database, the only thing we keep is the amount of overtime.
