# azhealthclub

#Note

composer.patches.json cp to vendor/cweagans/composer-patches (need test this)

#after you change menu, block, taxnomy

drush ea; drush cex -y;then push code

#we use default_content_deploy module sync some other entity content, like node...
#the document about how to use default_content_deploy

Export: drush dcder node
Import: drush dcdi --folder='sites/default/config/content/'

https://github.com/HBFCrew/default_content_deploy/blob/8.x-1.x/README.md


#before you work

if you see structure_sync.data update, must
drush cim -y;drush ia
