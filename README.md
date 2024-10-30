# plugin-woocommerce
Compensate plugin for Woocommerce



## Deploy to staging

Make your changes.
Then npm run build
Then upload files via sftp to staging site

## Deploy to Wordpress Plugin Directory

### Checkout the code from svn
svn co https://plugins.svn.wordpress.org/compensate-for-woocommerce woo-compensate

### Make code changes
Create a new github branch, make your changes and:

* update version in compensate.php
* update version in readme.txt
* add version to changelog in readme.txt
* update version in compansateInit.php (search for old version)
* update version in package.json


### Prepare deploy to wordpress
Generate assets

```
npm run build
```

Then manually copy all files to svn trunk directory that you checked out later.

Run
```
svn stat
```
and remove all files that are "new" ie with a ? in front of them

### Then commit changes to svn.

```
svn ci -m 'your commit message' --username compensate
```

Verify the app listing page: https://wordpress.org/plugins/compensate-for-woocommerce

