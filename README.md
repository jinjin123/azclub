# azhealthclub

#Note

composer.patches.json cp to vendor/cweagans/composer-patches (need test this)

#after you change menu, block, taxnomy

drush ea; drush cex -y;then push code

#we use default_content_deploy module sync some other entity content, like node...
#the document about how to use default_content_deploy
https://github.com/HBFCrew/default_content_deploy/blob/8.x-1.x/README.md

>Export node
```
dush dcde node
```
>Export node and its relation entity
```
dush dcder node
```
>Export single node
```
drush dcde node --entity_id=1,2,3...
```
>Export all site content, except block_content,menu_link_content,taxonomy_term,user,
and rmove the content not used in the site.
```
drush dcdes  --skip_entity_type=block_content,menu_link_content,taxonomy_term,user --force-update
```
>Import, if you want ovirride your site content, add the option --force-override
```
drush dcdi --folder='sites/default/config/content/'
```

#before you work

if you see structure_sync.data update, must
drush cim -y;drush ia
